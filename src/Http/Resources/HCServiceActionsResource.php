<?php
/**
 * @copyright 2018 interactivesolutions
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InteractiveSolutions:
 * E-mail: hello@interactivesolutions.lt
 * http://www.interactivesolutions.lt
 */

namespace HoneyComb\Scripts\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

/**
 * Class HCServiceActionsResource
 * @package HoneyComb\Scripts\Http\Resources
 */
class HCServiceActionsResource extends ResourceCollection
{
    /**
     * @var array
     */
    private $admin;

    /**
     * @var array
     */
    private $api;

    /**
     * @var array
     */
    private $front;

    /**
     * HCServiceActionsResource constructor.
     * @param array $data
     */
    public function __construct(Collection $data)
    {
        parent::__construct($data);

        if (isset($data['admin']))
            $this->admin = $data['admin'];
        else
            $this->admin = [];

        if (isset($data['api']))
            $this->api = $data['api'];
        else
            $this->api = [];

        if (isset($data['front']))
            $this->front = $data['front'];
        else
            $this->front = [];
    }

    /**
     * @return array
     */
    public function getFront(): array
    {
        return $this->front;
    }

    /**
     * @return array
     */
    public function getApi(): array
    {
        return $this->api;
    }

    /**
     * @return array
     */
    public function getAdmin(): array
    {
        return $this->admin;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        return get_object_vars($this);
    }
}
