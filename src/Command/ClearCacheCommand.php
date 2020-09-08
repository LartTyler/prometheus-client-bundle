<?php
	namespace DaybreakStudios\PrometheusClientBundle\Command;

	use DaybreakStudios\PrometheusClient\Adapter\AdapterInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\DependencyInjection\ContainerAwareInterface;
	use Symfony\Component\DependencyInjection\ContainerInterface;

	class ClearCacheCommand extends Command implements ContainerAwareInterface {
		/**
		 * @var ContainerInterface
		 */
		protected $container;

		/**
		 * {@inheritdoc}
		 */
		public function setContainer(ContainerInterface $container = null) {
			$this->container = $container;
		}

		/**
		 * {@inheritdoc}
		 */
		protected function configure() {
			$this
				->addArgument('adapter', InputArgument::REQUIRED, 'The service ID of the adapter to clear')
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
			$adapter = $this->container->get(
				$input->getArgument('adapter'),
				ContainerInterface::NULL_ON_INVALID_REFERENCE
			);

			if (!$adapter) {
				$io->error('Could not find service with ID ' . $input->getArgument('adapter'));

				return 1;
			} else if (!($adapter instanceof AdapterInterface)) {
				$io->error(
					'The service named ' .
					$input->getArgument('adapter') .
					' does not implement ' .
					AdapterInterface::class
				);

				return 1;
			}

			if ($keys = $input->getOption('key')) {
				foreach ($keys as $key) {
					if ($adapter->delete($key))
						$io->success('Deleted ' . $key . ' from cache');
				}
			}

			if ($prefixes = $input->getOption('prefix')) {
				foreach ($prefixes as $prefix) {
					foreach ($adapter->search($prefix) as $key => $value) {
						if ($output->isVerbose())
							$io->comment('Deleting ' . $key);

						$adapter->delete($key);
					}

					$io->success('Deleted keys prefixed by ' . $prefix . ' from cache');
				}
			}

			if (!$keys && !$prefixes) {
				if (!$adapter->clear())
					return 1;

				$io->success('Cleared all items from the cache');
			}

			return 0;
		}
	}