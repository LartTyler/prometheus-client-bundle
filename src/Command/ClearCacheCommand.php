<?php
	namespace DaybreakStudios\PrometheusClientBundle\Command;

	use DaybreakStudios\PrometheusClient\Adapter\AdapterInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

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
		 * {@inheritdoc}
		 */
		protected function configure() {
			$this
				->addOption(
					'key',
					'k',
					InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
					'A key to remove from the cache'
				)
				->addOption(
					'prefix',
					'p',
					InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
					'A key prefix to remove the cache'
				);
		}

		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @return int
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);

			if ($keys = $input->getOption('key')) {
				foreach ($keys as $key) {
					if ($this->adapter->delete($key))
						$io->success('Deleted ' . $key . ' from cache');
				}
			}

			if ($prefixes = $input->getOption('prefix')) {
				foreach ($prefixes as $prefix) {
					foreach ($this->adapter->search($prefix) as $key => $value) {
						if ($output->isVerbose())
							$io->comment('Deleting ' . $key);

						$this->adapter->delete($key);
					}

					$io->success('Deleted keys prefixed by ' . $prefix . ' from cache');
				}
			}

			if (!$keys && !$prefixes) {
				if (!$this->adapter->clear())
					return 1;

				$io->success('Cleared all items from the cache');
			}

			return 0;
		}
	}