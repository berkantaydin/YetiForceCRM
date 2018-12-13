<?php
/**
 * Logo class to handle files.
 *
 * @copyright YetiForce Sp. z o.o
 * @license   YetiForce Public License 3.0 (licenses/LicenseEN.txt or yetiforce.com)
 * @author    Mariusz Krzaczkowski <m.krzaczkowski@yetiforce.com>
 */

/**
 * Logo class to handle files.
 */
class MultiCompany_Logo_File extends Vtiger_Basic_File
{
	/**
	 * {@inheritdoc}
	 */
	public $storageName = 'MultiCompany';

	/**
	 * {@inheritdoc}
	 */
	public function getCheckPermission(\App\Request $request)
	{
		if (!App\Session::has('authenticated_user_id') || $request->isEmpty('record', true) || $request->isEmpty('key', true)) {
			throw new \App\Exceptions\NoPermitted('LBL_PERMISSION_DENIED');
		}
		return true;
	}

	/**
	 * Get company logo.
	 *
	 * @param \App\Request $request
	 *
	 * @throws \App\Exceptions\IllegalValue
	 * @throws \App\Exceptions\NoPermitted
	 */
	public function get(\App\Request $request)
	{
		$userModel = \App\User::getUserModel($request->getInteger('record'));
		$logo = $userModel->get('multiCompanyLogo');
		if ($logo['key'] !== $request->getByType('key', 2)) {
			throw new \App\Exceptions\NoPermitted('LBL_PERMISSION_DENIED');
		}
		$file = \App\Fields\File::loadFromInfo([
			'path' => ROOT_DIRECTORY . DIRECTORY_SEPARATOR . $logo['path'],
			'name' => $logo['name'],
		]);
		header('Pragma: public');
		header('Cache-Control: max-age=86400, public');
		header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
		header('Content-Type: ' . $file->getMimeType());
		header('Content-Transfer-Encoding: binary');
		header('Content-length: ' . $file->getSize());
		readfile($file->getPath());
	}
}
