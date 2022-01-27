<?php
namespace Drahak\Restful\Http;

use Nette\Http\RequestFactory;
use Nette\Http\Request;
use Nette\Http\IRequest;

/**
 * Api request factory
 * @author Drahomír Hanák
 */
class ApiRequestFactory 
{

	const OVERRIDE_HEADER = 'X-HTTP-Method-Override';
	const OVERRIDE_PARAM = '__method';

	/**
	 * @var RequestFactory
	 */
	private $factory;

	/**
	 * @param RequestFactory $factory 
	 */
	public function __construct(RequestFactory $factory)
	{
		$this->factory = $factory;
	}

	/**
	 * Create API HTTP request 
	 * @return IRequest 
	 */
	public function createHttpRequest()
	{
		$request = $this->factory->createHttpRequest();
		$url = $request->getUrl();

		return new Request(
			$url, NULL, $request->getPost(), $request->getFiles(), $request->getCookies(), $request->getHeaders(),
			$this->getPreferredMethod($request), $request->getRemoteAddress(), null,
			function () use ($request) { return $request->getRawBody(); }
		);
	}

	/**
	 * Get prederred method 
	 * @param  IRequest $request 
	 * @return string            
	 */
	protected function getPreferredMethod(IRequest $request)
	{
		$method = $request->getMethod();
		$isPost = $method === IRequest::POST;
		$header = $request->getHeader(self::OVERRIDE_HEADER);
		$param = $request->getQuery(self::OVERRIDE_PARAM);
		if ($header && $isPost) {
			return $header;
		}
		if ($param && $isPost) {
			return $param;
		}
		return $request->getMethod();
	}

}
