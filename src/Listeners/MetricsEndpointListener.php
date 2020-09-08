<?php
	namespace DaybreakStudios\PrometheusClientBundle\Listeners;

	use DaybreakStudios\PrometheusClient\CollectorRegistryInterface;
	use DaybreakStudios\PrometheusClient\Export\Render\TextRenderer;
	use Psr\Log\LoggerInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Event\RequestEvent;

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
		 * @var bool
		 */
		protected $force;

		/**
		 * @var LoggerInterface
		 */
		protected $logger;

		/**
		 * MetricsEndpointListener constructor.
		 *
		 * @param CollectorRegistryInterface $registry
		 * @param string                     $metricsEndpoint
		 * @param bool                       $force
		 * @param LoggerInterface            $logger
		 */
		public function __construct(
			CollectorRegistryInterface $registry,
			string $metricsEndpoint,
			bool $force,
			LoggerInterface $logger
		) {
			$this->registry = $registry;
			$this->metricsEndpoint = $metricsEndpoint;
			$this->force = $force;
			$this->logger = $logger;
		}

		/**
		 * @param RequestEvent $event
		 *
		 * @return void
		 */
		public function onKernelRequest(RequestEvent $event) {
			$request = $event->getRequest();

			if ($request->getPathInfo() !== $this->metricsEndpoint || $request->getMethod() !== Request::METHOD_GET)
				return;
			else if (!$this->force && $event->getResponse()) {
				$this->logger->warning(
					'Cannot respond to GET /metrics; response has already been set by another controller or ' .
					'listener. You can set dbstudios_prometheus.metrics.force to `true` to force responding to' .
					'requests even if another component sets a response first.',
				);

				return;
			}

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