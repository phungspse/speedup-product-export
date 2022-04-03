<?php

namespace PhungSpse\SpeedupProductExport\Console\Command;

use Magento\ImportExport\Model\Export\Entity\ExportInfo;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\ImportExport\Model\Export\Consumer as ExportConsumer;
use Magento\MysqlMq\Model\Message;
use Magento\MysqlMq\Model\MessageFactory;
use Magento\Framework\Serialize\Serializer\Json;

class ManualExportQueueMessage extends Command
{
    const QUEUE_MESSAGE_ID = 'queue-message-id';
    const QUEUE_MESSAGE_ID_SHORTCUT = 'm';

    /** @var MessageFactory */
    protected $messageFactory;

    /** @var ExportConsumer */
    protected $exportConsumer;

    /** @var Json */
    protected $json;

    public function __construct(
        MessageFactory $messageFactory,
        ExportConsumer $exportConsumer,
        Json $json
    ) {
        parent::__construct();

        $this->messageFactory = $messageFactory;
        $this->exportConsumer = $exportConsumer;
        $this->json = $json;
    }
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('phung-spse:export:manual-export');
        $this->setDescription('Manual run export process by queue message id');
        $this->setDefinition([
            new InputOption(
                self::QUEUE_MESSAGE_ID,
                self::QUEUE_MESSAGE_ID_SHORTCUT,
                InputOption::VALUE_REQUIRED,
                'Queue message id, get from table queue_message and topic_name = "import_export.export"'
            )
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queueMessageId = $input->getOption(self::QUEUE_MESSAGE_ID);

        /** @var Message $queueMessage */
        $queueMessage = $this->messageFactory->create();
        $queueMessage->load($queueMessageId);
        if (!$queueMessage->getId()) {
            $output->writeln(__('Not found queue message %1', $queueMessageId));
            return;
        }

        if ($queueMessage->getData('topic_name') != 'import_export.export') {
            $output->writeln(__('Queue message %1 is not an export topic', $queueMessageId));
            return;
        }

        $output->writeln(__('Start export for queue message %1', $queueMessageId));

        try {
            $data = $this->json->unserialize($queueMessage->getData('body'));
            $exportInfo = new ExportInfo();
            $exportInfo->setEntity($data['entity']);
            $exportInfo->setFileName($data['file_name']);
            $exportInfo->setFileFormat($data['file_format']);
            $exportInfo->setContentType($data['content_type']);
            $exportInfo->setExportFilter($data['export_filter']);
            $this->exportConsumer->process($exportInfo);
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }

        $output->writeln(__('Export done for queue message %1', $queueMessageId));
    }
}
