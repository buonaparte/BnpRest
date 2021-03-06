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

namespace BnpRest\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 * @licence MIT
 */
class ModuleOptions extends AbstractOptions
{
    /**
     * @var array
     */
    private $restRoutePrefixes = array();

    /**
     * @var array
     */
    private $exceptionMap = array();

    /**
     * @var bool
     */
    private $registerHttpMethodOverrideListener = false;

    /**
     * @return array
     */
    public function getRestRoutePrefixes()
    {
        return $this->restRoutePrefixes;
    }

    /**
     * @param array $restRoutePrefixes
     */
    public function setRestRoutePrefixes(array $restRoutePrefixes)
    {
        $this->restRoutePrefixes = $restRoutePrefixes;
    }

    /**
     * @param array $exceptionMap
     */
    public function setExceptionMap(array $exceptionMap)
    {
        $this->exceptionMap = $exceptionMap;
    }

    /**
     * @return array
     */
    public function getExceptionMap()
    {
        return $this->exceptionMap;
    }

    /**
     * @param bool $registerHttpMethodOverrideListener
     */
    public function setRegisterHttpMethodOverrideListener($registerHttpMethodOverrideListener)
    {
        $this->registerHttpMethodOverrideListener = (bool) $registerHttpMethodOverrideListener;
    }

    /**
     * @return bool
     */
    public function getRegisterHttpMethodOverrideListener()
    {
        return $this->registerHttpMethodOverrideListener;
    }
}
