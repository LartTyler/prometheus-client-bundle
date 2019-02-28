<?php
	namespace DaybreakStudios\PrometheusClientBundle;

	use DaybreakStudios\PrometheusClientBundle\DependencyInjection\CollectorCompilerPass;
	use DaybreakStudios\PrometheusClientBundle\DependencyInjection\DaybreakStudiosPrometheusClientExtension;
	use Symfony\Component\DependencyInjection\ContainerBuilder;
	use Symfony\Component\HttpKernel\Bundle\Bundle;

	class DaybreakStudiosPrometheusClientBundle extends Bundle {
		/**
		 * {@inheritdoc}
		 */
		public function build(ContainerBuilder $container) {
			parent::build($container);

			$container->addCompilerPass(new CollectorCompilerPass());
		}

		/**
		 * @return DaybreakStudiosPrometheusClientExtension
		 */
		public function getContainerExtension() {
			return new DaybreakStudiosPrometheusClientExtension();
		}
	}