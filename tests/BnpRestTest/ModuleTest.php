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

namespace BnpRestTest;

use PHPUnit_Framework_TestCase;
use Zend\ServiceManager\ServiceManager;
use BnpRest\Module;
use BnpRest\Mvc\HttpExceptionListener;
use BnpRest\Options\ModuleOptions;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\Module
 */
class ModuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;

    /**
     * @var \Zend\EventManager\EventManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    public function testGetConfig()
    {
        $module = new Module();

        $this->assertInternalType('array', $module->getConfig());
        $this->assertSame($module->getConfig(), unserialize(serialize($module->getConfig())), 'Config is serializable');
    }

    public function testListenersAreRegistered()
    {
        $event   = $this->getEvent();
        $options = new ModuleOptions(array(
            'register_http_method_override_listener' => true
        ));

        $this->serviceLocator->setService('BnpRest\Options\ModuleOptions', $options);

        // --------------------------------------------------------------------------------
        // Test that HTTP exception listener is registered
        // --------------------------------------------------------------------------------

        $this->serviceLocator->setService('BnpRest\Mvc\HttpExceptionListener', $this->getMock(HttpExceptionListener::class));

        $this->eventManager->expects($this->at(0))
                           ->method('attachAggregate')
                           ->with($this->isInstanceOf('BnpRest\Mvc\HttpExceptionListener'));

        // --------------------------------------------------------------------------------
        // Test that HTTP response listener is registered
        // --------------------------------------------------------------------------------

        $this->serviceLocator->setService(
            'BnpRest\Mvc\ResourceResponseListener',
            $this->getMock('BnpRest\Mvc\ResourceResponseListener')
        );

        $this->eventManager->expects($this->at(1))
                           ->method('attachAggregate')
                           ->with($this->isInstanceOf('BnpRest\Mvc\ResourceResponseListener'));

        // --------------------------------------------------------------------------------
        // Test the HTTP method override listener is registered
        // --------------------------------------------------------------------------------

        $this->serviceLocator->setService(
            'BnpRest\Mvc\HttpMethodOverrideListener',
            $this->getMock('BnpRest\Mvc\HttpMethodOverrideListener')
        );

        $this->eventManager->expects($this->at(2))
                           ->method('attachAggregate')
                           ->with($this->isInstanceOf('BnpRest\Mvc\HttpMethodOverrideListener'));

        $module = new Module();
        $module->onBootstrap($event);
    }

    /**
     * @return \Zend\EventManager\EventInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getEvent()
    {
        $this->serviceLocator = new ServiceManager();
        $this->eventManager   = $this->getMock('Zend\EventManager\EventManagerInterface');

        $application = $this->getMock('Zend\Mvc\Application', array(), array(), '', false);
        $application->expects($this->any())
                    ->method('getServiceManager')
                    ->will($this->returnValue($this->serviceLocator));

        $application->expects($this->any())
                    ->method('getEventManager')
                    ->will($this->returnValue($this->eventManager));

        $event = $this->getMock('Zend\EventManager\EventInterface');
        $event->expects($this->any())->method('getTarget')->will($this->returnValue($application));

        return $event;
    }
}
