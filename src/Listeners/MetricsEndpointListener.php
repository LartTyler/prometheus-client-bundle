<?php
	namespace DaybreakStudios\PrometheusClientBundle\Listeners;

	use DaybreakStudios\PrometheusClientBundle\Prometheus\CollectorRegistry;
	use Prometheus\RenderTextFormat;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Event\GetResponseEvent;

	class MetricsEndpointListener {
		/**
		 * @var CollectorRegistry
		 */
		protected $registry;

		/**
		 * @var string
		 */
		protected $metricsEndpoint;

		/**
		 * MetricsEndpointListener constructor.
		 *
		 * @param CollectorRegistry $registry
		 * @param string            $metricsEndpoint
		 */
		public function __construct(CollectorRegistry $registry, $metricsEndpoint) {
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

			$renderer = new RenderTextFormat();
			$response = new Response(
				$renderer->render($this->registry->collect()),
				Response::HTTP_OK,
				[
					'Content-Type' => RenderTextFormat::MIME_TYPE,
				]
			);

			$event->setResponse($response);
		}
	}