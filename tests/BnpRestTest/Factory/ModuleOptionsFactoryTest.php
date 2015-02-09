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

namespace BnpRestTest\Factory;

use PHPUnit_Framework_TestCase;
use Zend\Http\Response as HttpResponse;
use BnpRest\Factory\ModuleOptionsFactory;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\Factory\ModuleOptionsFactory
 */
class ModuleOptionsFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testFactory()
    {
        $options = array(
            'bnp-rest' => array(
                'exception_map' => array(
                    'foo' => 'bar'
                ),
                'register_http_method_override_listener' => true
            )
        );

        $serviceLocator = $this->getMock('Zend\ServiceManager\ServiceLocatorInterface');

        $serviceLocator->expects($this->once())
                       ->method('get')
                       ->with('Config')
                       ->will($this->returnValue($options));

        $factory  = new ModuleOptionsFactory();
        $instance = $factory->createService($serviceLocator);

        $this->assertInstanceOf('BnpRest\Options\ModuleOptions', $instance);
        $this->assertEquals($options['bnp-rest']['exception_map'], $instance->getExceptionMap());
        $this->assertTrue($instance->getRegisterHttpMethodOverrideListener());
    }
}
