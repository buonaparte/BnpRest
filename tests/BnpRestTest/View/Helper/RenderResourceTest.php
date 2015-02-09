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

namespace BnpRestTest\View\Helper;

use PHPUnit_Framework_TestCase;
use Zend\View\Helper\ViewModel as ViewModelHelper;
use Zend\View\Renderer\RendererInterface;
use BnpRest\View\Helper\RenderResource;
use BnpRest\View\Model\ResourceViewModel;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\View\Helper\RenderResource
 */
class RenderResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Zend\View\Renderer\RendererInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $view;

    /**
     * @var RenderResource
     */
    private $helper;

    public function setUp()
    {
        $this->view = $this->getMock('Zend\View\Renderer\RendererInterface', array('render', 'viewModel', 'getEngine', 'setResolver'));

        $this->helper = new RenderResource();
        $this->helper->setView($this->view);
    }

    public function testCanRenderAnotherResourceWithSpecifiedVersion()
    {
        $this->view->expects($this->never())->method('viewModel');

        $self = $this;
        $this->view->expects($this->once())
                   ->method('render')
                   ->with($this->callback(function(ResourceViewModel $resourceViewModel) use ($self) {
                       $self->assertEquals('v2/foo.php', $resourceViewModel->getTemplate());
                       $self->assertEquals('v2', $resourceViewModel->getVersion());
                       $self->assertEquals(array('key' => 'value'), $resourceViewModel->getVariables());

                       return true;
                   }));

        $helper = $this->helper;
        $helper('foo', array('key' => 'value'), 'v2');
    }

    public function testCanRenderAnotherResourceWithoutVersion()
    {
        $currentViewModel = new ResourceViewModel(array(), array('version' => 'default'));

        $viewModelHelper = $this->getMock('Zend\View\Helper\ViewModel');
        $viewModelHelper->expects($this->once())->method('getCurrent')->will($this->returnValue($currentViewModel));

        $this->view->expects($this->once())
                   ->method('viewModel')
                   ->will($this->returnValue($viewModelHelper));

        $self = $this;
        $this->view->expects($this->once())
                   ->method('render')
                   ->with($this->callback(function(ResourceViewModel $resourceViewModel) use ($self) {
                       $self->assertEquals('default/foo.php', $resourceViewModel->getTemplate());
                       $self->assertEquals('default', $resourceViewModel->getVersion());
                       $self->assertEquals(array('key' => 'value'), $resourceViewModel->getVariables());

                       return true;
                   }));

        $helper = $this->helper;
        $helper('foo', array('key' => 'value'));
    }
}
