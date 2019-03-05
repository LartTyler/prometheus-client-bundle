<?php
	namespace DaybreakStudios\PrometheusClientBundle\Listeners;

	use DaybreakStudios\PrometheusClient\CollectorRegistryInterface;
	use DaybreakStudios\PrometheusClient\Export\Render\TextRenderer;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Event\GetResponseEvent;

	class MetricsEndpointListener {
		/**
		 * @var CollectorRegistryInterface
		 */
		protected $registry;

		/**
		 * @var string
		 */
		protected $metricsEndpoint;

		/**
		 * MetricsEndpointListener constructor.
		 *
		 * @param CollectorRegistryInterface $registry
		 * @param string                     $metricsEndpoint
		 */
		public function __construct(CollectorRegistryInterface $registry, $metricsEndpoint) {
			$this->registry = $registry;
			$this->metricsEndpoint = $metricsEndpoint;
		}

		/**
		 * @param GetResponseEvent $event
		 *
		 * @return void
		 */
		public function onKernelRequest(GetResponseEvent $event) {
			if ($event->getResponse())
				return;

			$request = $event->getRequest();

			if ($request->getPathInfo() !== $this->metricsEndpoint)
				return;

			$renderer = new TextRenderer();
			$response = new Response(
				$renderer->render($this->registry->collect()),
				Response::HTTP_OK,
				[
					'Content-Type' => $renderer->getMimeType(),
				]
			);

			$event->setResponse($response);
		}
	}