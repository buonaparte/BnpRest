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

return array(
    'service_manager' => array(
        'invokables' => array(
            'BnpRest\Mvc\ResourceResponseListener' => 'BnpRest\Mvc\ResourceResponseListener'
        ),

        'factories' => array(
            'BnpRest\Mvc\HttpExceptionListener' => 'BnpRest\Factory\HttpExceptionListenerFactory',
            'BnpRest\Options\ModuleOptions' => 'BnpRest\Factory\ModuleOptionsFactory',
            'BnpRest\View\Renderer\ResourceRenderer' => 'BnpRest\Factory\ResourceRendererFactory',
            'BnpRest\View\Strategy\ResourceStrategy' => 'BnpRest\Factory\ResourceStrategyFactory'
        )
    ),

    'controller_plugins' => array(
        'factories' => array(
            'BnpRest\Mvc\Controller\Plugin\ValidateIncomingData' => 'BnpRest\Mvc\Controller\Plugin\ValidateIncomingData',
            'BnpRest\Mvc\Controller\Plugin\HydrateObject' => 'BnpRest\Mvc\Controller\Plugin\HydrateObject'
        ),

        'aliases' => array(
            'validateIncomingData' => 'BnpRest\Mvc\Controller\Plugin\ValidateIncomingData',
            'hydrateObject' => 'BnpRest\Mvc\Controller\Plugin\HydrateObject'
        )
    ),

    'view_helpers' => array(
        'invokables' => array(
            'BnpRest\View\Helper\RenderPaginator' => 'BnpRest\View\Helper\RenderPaginator',
            'BnpRest\View\Helper\RenderResource' => 'BnpRest\View\Helper\RenderResource'
        ),

        'aliases' => array(
            'renderPaginator' => 'BnpRest\View\Helper\RenderPaginator',
            'renderResource' => 'BnpRest\View\Helper\RenderResource'
        )
    ),

    'view_manager' => array(
        'strategies' => array(
            'BnpRest\View\Strategy\ResourceStrategy'
        )
    ),

    'bnp-rest' => array()
);
