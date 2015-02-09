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

namespace BnpRestTest\View\Renderer;

use PHPUnit_Framework_TestCase;
use Zend\View\Helper\ViewModel as ViewModelHelper;
use BnpRest\View\Model\ResourceViewModel;
use BnpRest\View\Renderer\ResourceRenderer;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\View\Renderer\ResourceRenderer
 */
class ResourceRendererTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resolver;

    public function setUp()
    {
        $this->resolver = $this->getMock('Zend\View\Resolver\ResolverInterface');
    }

    public function testCanCheckRootModel()
    {
        $viewModel = $this->getMock('Zend\View\Model\ModelInterface');

        $viewModelHelper = new ViewModelHelper();
        $viewModelHelper->setRoot($viewModel);
        $viewModelHelper->setCurrent($viewModel);

        $helperPluginManager = $this->getMock('Zend\View\HelperPluginManager', array(), array(), '', false);
        $helperPluginManager->expects($this->any())
                            ->method('get')
                            ->with('viewModel')
                            ->will($this->returnValue($viewModelHelper));

        $resourceRenderer = new ResourceRenderer($this->resolver, $helperPluginManager);

        $this->assertTrue($resourceRenderer->isRootTemplate());

        // Set another model for the current
        $viewModelHelper->setCurrent($this->getMock('Zend\View\Model\ModelInterface'));

        $this->assertFalse($resourceRenderer->isRootTemplate());
    }

    public function testCanRender()
    {
        $previousViewModel = new ResourceViewModel(array('bar' => 'baz'));
        $viewModel         = new ResourceViewModel(array('foo' => 'bar'), array('template' => 'foo'));

        $viewModelHelper = $this->getMock('Zend\View\Helper\ViewModel', array(), array(), '', false);

        $viewModelHelper->expects($this->at(0))->method('getCurrent')->willReturn($previousViewModel);

        $helperPluginManager = $this->getMock('Zend\View\HelperPluginManager', array(), array(), '', false);
        $helperPluginManager->expects($this->any())
                            ->method('get')
                            ->with('viewModel')
                            ->will($this->returnValue($viewModelHelper));

        $viewModelHelper->expects($this->at(1))->method('setCurrent')->with($viewModel);
        $viewModelHelper->expects($this->at(2))->method('setCurrent')->with($previousViewModel); // Make sure it reset

        $this->resolver->expects($this->once())
                       ->method('resolve')
                       ->with('foo')
                       ->will($this->returnValue(__DIR__ . '/../../Asset/view/foo.php'));

        $resourceRenderer = new ResourceRenderer($this->resolver, $helperPluginManager);

        $result = $resourceRenderer->render($viewModel);

        $this->assertEquals(array('foo' => 'bar'), $result);
    }
}
