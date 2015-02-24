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
use Zend\Paginator\Adapter\ArrayAdapter;
use Zend\Paginator\Paginator;
use BnpRest\View\Helper\RenderPaginator;

/**
 * @license MIT
 * @author  Michaël Gallego <mic.gallego@gmail.com>
 *
 * @group Coverage
 * @covers \BnpRest\View\Helper\RenderPaginator
 */
class RenderPaginatorTest extends PHPUnit_Framework_TestCase
{
    public function testCanOutputPaginator()
    {
        $paginator = new Paginator(new ArrayAdapter(array(1, 2, 3, 4, 5, 6)));
        $paginator->setItemCountPerPage(3);
        $paginator->setCurrentPageNumber(2);

        $helper = new RenderPaginator();
        $result = $helper($paginator);

        $expected = array(
            'total_item_count'  => 6,
            'item_count_per_page' => 3,
            'current_page_number'  => 2,
            'current_item_count' => 3,
        );

        $this->assertEquals($expected, $result);
    }
}
