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
 * Class HCPackageResource
 * @package HoneyComb\Scripts\Http\Resources
 */
class HCPackageResource extends ResourceCollection
{
    /**
     * Official package name
     * @var string
     */
    private $packageName;

    /**
     * Main namespace
     * @var string
     */
    private $namespace;

    /**
     * Forms config
     * @var array
     */
    private $formData;

    /**
     * Admin menu config
     * @var array
     */
    private $adminMenu;

    /**
     * List of routes which will be later loaded and cached
     *
     * @var array
     */
    private $routes;

    /**
     * List of permissions available for this package
     *
     * @var array
     */
    private $permissions;

    /**
     * Default package configuration
     * @var array
     */
    private $data;

    /**
     * Service name
     * @var string
     */
    private $serviceName;

    /**
     * HCPackageResource constructor.
     * @param Collection $data
     */
    public function __construct(Collection $data)
    {
        parent::__construct($data);

        $this->data = $data;

        $this->packageName = $data['general']['packageName'];
        $this->namespace = $data['general']['namespace'];

        $this->formData = $data['formData'];
        $this->adminMenu = $data['adminMenu'];

        $this->routes = $data['routes'];
        $this->permissions = $data['acl']['permissions'];
    }

    /**
     * @return string
     */
    public function getPackageName(): string
    {
        return $this->packageName;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return $this->formData;
    }

    /**
     * @return array
     */
    public function getAdminMenu(): array
    {
        return $this->adminMenu;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * Getting namespace for controller
     *
     * @param bool $full
     * @return string
     */
    public function getNamespaceForAdminController(bool $full = false): string
    {
        $ns = $this->namespace . 'Http\\Controllers\\Admin';

        if ($full)
            $ns .= '\\' . $this->serviceName . 'Controller';

        return $ns;
    }

    /**
     * Getting namespace for model
     *
     * @param bool $full
     * @return string
     */
    public function getNamespaceForModel(bool $full = false): string
    {
        $ns = $this->namespace . 'Models';

        if ($full)
            $ns .= '\\' . $this->serviceName;

        return $ns;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getNamespaceForForm(bool $full = false): string
    {
        $ns = $this->namespace . 'Forms';

        if ($full)
            $ns .= '\\' . $this->serviceName . 'Form';

        return $ns;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getNamespaceForRepository(bool $full = false): string
    {
        $ns = $this->namespace . 'Repositories';

        if ($full)
            $ns .= '\\' . $this->serviceName . 'Repository';

        return $ns;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getNamespaceForService(bool $full = false): string
    {
        $ns = $this->namespace . 'Services';

        if ($full)
            $ns .= '\\' . $this->serviceName . 'Service';

        return $ns;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getNameSpaceForRequest(bool $full = false): string
    {
        $ns = $this->namespace . 'Http\\Requests';

        if ($full)
            $ns .= '\\' . $this->serviceName . 'Request';

        return $ns;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName(string $serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @return array
     */
    protected function jsonData(): array
    {
        // TODO: Implement jsonData() method.
    }
}
