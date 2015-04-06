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

namespace BnpRest;

use Zend\EventManager\EventInterface;
use Zend\Http\Request;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use BnpRest\Options\ModuleOptions;

/**
 * Module
 *
 * @license MIT
 */
class Module implements BootstrapListenerInterface, ConfigProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function onBootstrap(EventInterface $event)
    {
        /* @var $application \Zend\Mvc\Application */
        $application    = $event->getTarget();
        $serviceManager = $application->getServiceManager();
        $eventManager   = $application->getEventManager();

        /* @var ModuleOptions $moduleOptions */
        $moduleOptions = $serviceManager->get('BnpRest\Options\ModuleOptions');

        $request = $application->getRequest();
        if (! $request instanceof Request || ! $moduleOptions->getRestRoutePrefixes()) {
            return;
        }

        $requestUri = $request->getUriString();
        foreach ($moduleOptions->getRestRoutePrefixes() as $routePrefix) {
            if (false !== strstr($requestUri, $routePrefix)) {
                $eventManager->attachAggregate($serviceManager->get('BnpRest\Mvc\HttpExceptionListener'));
                $eventManager->attachAggregate($serviceManager->get('BnpRest\Mvc\ResourceResponseListener'));

                if ($moduleOptions->getRegisterHttpMethodOverrideListener()) {
                    $eventManager->attachAggregate($serviceManager->get('BnpRest\Mvc\HttpMethodOverrideListener'));
                }

                return;
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
