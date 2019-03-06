<?php
	namespace DaybreakStudios\PrometheusClientBundle\Command;

	use DaybreakStudios\PrometheusClient\Adapter\AdapterInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;

	class ClearCacheCommand extends Command {
		/**
		 * @var AdapterInterface
		 */
		protected $adapter;

		/**
		 * ClearCacheCommand constructor.
		 *
		 * @param AdapterInterface $adapter
		 */
		public function __construct(AdapterInterface $adapter) {
			parent::__construct();

			$this->adapter = $adapter;
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return int
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			if (!$this->adapter->clear())
				return 1;

			return 0;
		}
	}