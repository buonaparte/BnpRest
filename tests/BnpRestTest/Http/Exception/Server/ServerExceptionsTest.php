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

namespace BnpRestTest\Http\Exception\Client;

use PHPUnit_Framework_TestCase;
use BnpRest\Http\Exception;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\Http\Exception\Server\InternalServerErrorException
 * @covers \BnpRest\Http\Exception\Server\NotImplementedException
 * @covers \BnpRest\Http\Exception\Server\ServiceUnavailableException
 */
class ServerExceptionsTest extends PHPUnit_Framework_TestCase
{
    public function exceptionProvider()
    {
        return array(
            array(
                'exception'  => 'BnpRest\Http\Exception\Server\InternalServerErrorException',
                'statusCode' => 500
            ),
            array(
                'exception'  => 'BnpRest\Http\Exception\Server\NotImplementedException',
                'statusCode' => 501
            ),
            array(
                'exception'  => 'BnpRest\Http\Exception\Server\ServiceUnavailableException',
                'statusCode' => 503
            ),
        );
    }

    /**
     * @dataProvider exceptionProvider
     */
    public function testServerException($exception, $statusCode)
    {
        /* @var \BnpRest\Http\Exception\ServerErrorException $exception */
        $exception = new $exception();

        $this->assertEquals($statusCode, $exception->getCode());
    }
}
