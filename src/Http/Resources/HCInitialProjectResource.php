<?php
/**
 * @copyright 2018 innovationbase
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
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

namespace HoneyComb\Scripts\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class HCInitialProjectResource
 * @package HoneyComb\Scripts\Http\Resources
 */
class HCInitialProjectResource extends ResourceCollection
{
    private $packageName = "";

    private $namespaceComposer = "App\\\\";
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getFolderList(): array
    {
        return $this->data['folders'];
    }

    /**
     * @return array
     */
    public function getFilesList(): array
    {
        return $this->data['files'];
    }

    /**
     * @return array
     */
    public function jsonData(): array
    {
        return get_object_vars($this);
    }
}
