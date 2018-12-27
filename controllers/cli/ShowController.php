<?php

class ShowController extends MY_Controller {
	protected $console;
	protected $padding;
	protected $results = [];
	protected $files = [];

	protected $path = false;
	protected $details = false;
	protected $tag = 'show';

	public function __construct()
	{
		parent::__construct();

		$this->console = new League\CLImate\CLImate;
		$this->padding = $this->console->padding(40)->char(' ');
	}

	public function packagesCliAction($arg=null)
	{
		$autoload = load_config('autoload','autoload');

		$orange_paths = explode(PATH_SEPARATOR,rtrim(APPPATH,'/').PATH_SEPARATOR.implode(PATH_SEPARATOR,$autoload['packages']));

		foreach ($orange_paths as $path) {
			$this->console->out($path);
		}

	}

	/**
		Show all available validation classes. Use optional -p to show path instead of help
	 */
	public function validateCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/libraries/validations/Validate_(.*).php');
	}

	/**
		Show all available pear classes. Use optional -p to show path instead of help
	 */
	public function pearCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/libraries/pear_plugins/Pear_(.*).php');
	}

	/**
		Show all available filter classes. Use optional -p to show path instead of help
	 */
	public function filterCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/libraries/filters/Filter_(.*).php');
	}

	/**
		Show all available models classes. Use optional -p to show path instead of help
	 */
	public function modelsCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/models/(.*)_model.php');
	}

	/**
		Show all available libraries classes. Use optional -p to show path instead of help
	 */
	public function controllersCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/controllers/(.*)Controller.php');
	}

	public function controller_traitsCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/controllers/traits/(.*)controller_trait.php');
	}

	public function middlewareCliAction($arg=null)
	{
		$this->options($arg)->loop_over('(.*)/middleware/(.*)Middleware.php');
	}

	protected function options($arg)
	{
		if ($arg == '_p' || $arg == '-p') {
			$this->path = true;
		} elseif (is_string($arg)) {
			$this->details = $arg;
		}
		
		return $this;
	}

	protected function loop_over($regex)
	{
		$autoload = load_config('autoload','autoload');

		$orange_paths = explode(PATH_SEPARATOR,rtrim(APPPATH,'/').PATH_SEPARATOR.implode(PATH_SEPARATOR,$autoload['packages']));
		
		foreach ($orange_paths as $path) {
			$this->globr($path);
		}

		foreach ($this->files as $name=>$path) {
			if (preg_match_all('#^'.$regex.'$#im', $path, $matches, PREG_SET_ORDER, 0)) {
				if (is_array($matches)) {
					$this->get_path($name,$path);
					$this->get_help($name,$path,'@help');
					$this->get_help_between($name,$path,'@details');
				}
			}
		}

		foreach ($this->results as $name=>$entry) {
			if ($this->path) {
				$this->padding->label($name)->result($entry['path']);
			} elseif ($this->details) {
				if ($name == $this->details) {
					$this->console->blue($name.' Detailed Help');
					$this->console->out($this->results[$name]['details']);
				}
			} else {
				if (count($entry['help'])) {
					foreach ($entry['help'] as $help) {
						$this->padding->label($name)->result($help);
						$name = '';
					}
				} else {
					$this->padding->label($name)->result('');
				}
			}
		}
	}

	protected function get_path($name,$path)
	{
		$this->results[$name]['path'] = $path;
	}

	protected function get_help($name,$filepath,$tag)
	{
		$help = [];

		if (preg_match_all('/'.$tag.' (.*)/m', file_get_contents($filepath), $matches, PREG_SET_ORDER, 0)) {
			foreach ($matches as $m) {
				$help[] = $m[1];
			}
		}

		$this->results[$name]['help'] = $help;
	}

	protected function get_help_between($name,$filepath,$tag)
	{
		$details = '';

		if (preg_match_all('/'.$tag.'(.*)'.$tag.'/ims',file_get_contents($filepath), $matches, PREG_SET_ORDER, 0)) {
			$details = trim($matches[0][1]);
		}

		$this->results[$name]['details'] = $details;
	}

	protected function globr($path)
	{
		if (file_exists($path)) {
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),RecursiveIteratorIterator::SELF_FIRST);
			foreach ($files as $f) {
				if ($f->getExtension() == 'php') {
					$this->files[$f->getBasename('.php')] = $f->getRealPath();
				}
			}
		}
	}

}
