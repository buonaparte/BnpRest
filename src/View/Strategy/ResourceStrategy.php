<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace BnpRest\View\Strategy;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Response as HttpResponse;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use BnpRest\View\Model\ResourceViewModel;
use BnpRest\View\Renderer\ResourceRenderer;

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class ResourceStrategy extends AbstractListenerAggregate
{
    /**
     * @var ResourceRenderer
     */
    private $renderer;

    /**
     * @param ResourceRenderer $renderer
     */
    public function __construct(ResourceRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $eventManager, $priority = 1)
    {
        $sharedManager = $eventManager->getSharedManager();
        $sharedManager->attach('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH, array($this, 'setTemplate'), -100);

        $this->listeners[] = $eventManager->attach(ViewEvent::EVENT_RENDERER, array($this, 'selectRenderer'), $priority);
        $this->listeners[] = $eventManager->attach(ViewEvent::EVENT_RESPONSE, array($this, 'injectResponse'), $priority);
    }

    /**
     * @internal Override the template
     * @param MvcEvent $event
     */
    public function setTemplate(MvcEvent $event)
    {
        $model = $event->getResult();

        if (!$model instanceof ResourceViewModel) {
            return;
        }

        // We need to prepend the template by the version
        $version  = $this->extractApiVersion($event);
        $template = $version . '/' . $model->getTemplate() . '.php';

        $model->setTemplate($template);
    }

    /**
     * Detect if we should use the ResourceRenderer based on model type
     *
     * @internal
     * @param  ViewEvent $event
     * @return ResourceRenderer|null
     */
    public function selectRenderer(ViewEvent $event)
    {
        if (!$event->getModel() instanceof ResourceViewModel) {
            // no ResourceModel; do nothing
            return;
        }

        /** @var \Zend\View\HelperPluginManager $helperPluginManager */
        $helperPluginManager = $this->renderer->getHelperPluginManager();
        $helperPluginManager->setRenderer($this->renderer);

        // If we have a ResourceViewModel, we set it as the "root" view model in the view model helper. This allows
        // to differentiate between a nested context or not, in the view
        /** @var \Zend\View\Helper\ViewModel $viewModelHelper */
        $viewModelHelper = $helperPluginManager->get('viewModel');
        $viewModel       = $event->getModel();

        $viewModelHelper->setRoot($viewModel);
        $viewModelHelper->setCurrent($viewModel);

        return $this->renderer;
    }

    /**
     * Inject the response as a JSON payload and appropriate Content-Type header. It also sets proper
     * header response based on the request method
     *
     * @internal
     * @param  ViewEvent $event
     * @return void
     */
    public function injectResponse(ViewEvent $event)
    {
        $renderer = $event->getRenderer();

        if ($renderer !== $this->renderer) {
            // Discovered renderer is not ours; do nothing
            return;
        }

        /** @var HttpRequest $request */
        $request = $event->getRequest();
        /* @var HttpResponse $response */
        $response = $event->getResponse();

        $result = json_encode($event->getResult());

        // If we have a GET request, we compute an ETag, and check against the current ETag, if any
        if ($request->getMethod() === HttpRequest::METHOD_GET) {
            $etag = md5($result);
            $response->getHeaders()->addHeaderLine('Etag', $etag);

            // Let's compare the If-None-Match value (if any) with the computed Etag to check if we can return early
            // an empty response
            $ifNoneMatch = $request->getHeader('If-None-Match');

            if ($ifNoneMatch && $ifNoneMatch->getFieldValue() === $etag) {
                $response->setContent('');
                $response->setStatusCode(304);

                return;
            }
        }

        $response->setContent($result);
        $response->getHeaders()->addHeaderLine('Content-Type', 'application/json; charset=utf-8');
    }

    /**
     * Get the API version from the request
     *
     * @TODO: for now we do not do versioning, but this can be used to parse Accept header or read API
     *
     * @param  MvcEvent $event
     * @return string
     */
    private function extractApiVersion(MvcEvent $event)
    {
        /** @var ResourceViewModel $viewModel */
        $viewModel = $event->getResult();

        // If a version has explicitly been set, it takes precedence over any other version
        if ($version = $viewModel->getVersion()) {
            return $version;
        }

        // @TODO: extract from URL or headers
        $version = 'default';
        $viewModel->setVersion($version);

        return $version;
    }
}
