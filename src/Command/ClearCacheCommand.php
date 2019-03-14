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
				->addOption('key', 'k', InputOption::VALUE_REQUIRED, 'A key to remove from the cache; may be repeated')
				->addOption(
					'prefix',
					'p',
					InputOption::VALUE_REQUIRED,
					'A key prefix to remove the cache; may be repeated'
				);
		}

		/**
		 * @param InputInterface $input
		 * @param OutputInterface $output
		 *
		 * @return int
		 */
		protected function execute(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);

			if ($keys = $input->getOption('key')) {
				foreach ($keys as $key) {
					if ($this->adapter->delete($key))
						$io->comment('Deleted ' . $key . ' from cache');
				}
			}

			if ($prefixes = $input->getOption('prefix')) {
				foreach ($prefixes as $prefix) {
					foreach ($this->adapter->search($prefix) as $key)
						$this->adapter->delete($key);

					$io->comment('Deleted keys prefixed by ' . $prefix . ' from cache');
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