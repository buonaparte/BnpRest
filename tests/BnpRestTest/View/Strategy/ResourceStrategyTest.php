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

namespace BnpRestTest\View\Strategy;

use PHPUnit_Framework_TestCase;
use Zend\Mvc\MvcEvent;
use Zend\View\ViewEvent;
use BnpRest\View\Model\ResourceViewModel;
use BnpRest\View\Strategy\ResourceStrategy;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\View\Strategy\ResourceStrategy
 */
class ResourceStrategyTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceRenderer;

    /**
     * @var ResourceStrategy
     */
    private $resourceStrategy;

    public function setUp()
    {
        $this->resourceRenderer = $this->getMock('BnpRest\View\Renderer\ResourceRenderer', array(), array(), '', false);
        $this->resourceStrategy = new ResourceStrategy($this->resourceRenderer);
    }

    public function testAttachToCorrectEvents()
    {
        $sharedManager = $this->getMock('Zend\EventManager\SharedEventManagerInterface');
        $eventManager  = $this->getMock('Zend\EventManager\EventManagerInterface');

        $eventManager->expects($this->at(0))->method('getSharedManager')->will($this->returnValue($sharedManager));
        $eventManager->expects($this->at(1))->method('attach')->with(ViewEvent::EVENT_RENDERER);
        $eventManager->expects($this->at(2))->method('attach')->with(ViewEvent::EVENT_RESPONSE);

        $sharedManager->expects($this->once())
                     ->method('attach')
                     ->with('Zend\Stdlib\DispatchableInterface', MvcEvent::EVENT_DISPATCH);

        $this->resourceStrategy->attach($eventManager);
    }

    public function testDoNotSetTemplateIfNotResourceViewModel()
    {
        $model = $this->getMock('Zend\View\Model\ModelInterface');
        $model->expects($this->never())->method('setTemplate');

        $mvcEvent = new MvcEvent();
        $mvcEvent->setResult($model);

        $this->resourceStrategy->setTemplate($mvcEvent);
    }

    public function testDoNotSelectRenderIfNotResourceViewModel()
    {
        $model = $this->getMock('Zend\View\Model\ModelInterface');

        $viewEvent = new ViewEvent();
        $viewEvent->setModel($model);

        $this->assertNull($this->resourceStrategy->selectRenderer($viewEvent));
    }

    public function testProperlyFillViewModelHelperIfRendererIsSelected()
    {
        $model = new ResourceViewModel();

        $viewEvent = new ViewEvent();
        $viewEvent->setModel($model);

        $viewModelHelper = $this->getMock('Zend\View\Helper\ViewModel', array(), array(), '', false);
        $viewModelHelper->expects($this->once())->method('setRoot')->with($model);
        $viewModelHelper->expects($this->once())->method('setCurrent')->with($model);

        $helperManager = $this->getMock('Zend\View\HelperPluginManager', array(), array(), '', false);
        $helperManager->expects($this->once())->method('setRenderer')->with($this->resourceRenderer);
        $helperManager->expects($this->once())->method('get')->with('viewModel')->willReturn($viewModelHelper);

        $this->resourceRenderer->expects($this->once())->method('getHelperPluginManager')->willReturn($helperManager);

        $this->assertSame($this->resourceRenderer, $this->resourceStrategy->selectRenderer($viewEvent));
    }
}
