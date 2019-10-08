<?php


namespace OuZhou\LaravelToolGenerator\Console\Commands\Resources\Controller;


class Templates extends TemplatesConfig
{
	/**
	 * Function: common
	 * Notes: 通用模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  11:13
	 * @return string
	 */
	protected static function common()
	{
		return <<<CODE
<?php

namespace App\Http\Controllers@{namespace};

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use @{actionBasePath}@{modelNameUcfirst}Action;
use @{serviceBasePath}@{modelNameUcfirst}Service;
use @{repositoryBasePath}@{modelNameUcfirst}Repository;

class @{controllerName} extends Controller
{
	# CUD 操作
	private $@{modelName}Action;
	# R 操作
	private $@{modelName}Repository;
	# service 核心操作
	private $@{modelName}Service;
	
	# 注入依赖
	public function __construct(
		@{modelNameUcfirst}Action $@{modelName}Action,
		@{modelNameUcfirst}Service $@{modelName}Service,
		@{modelNameUcfirst}Repository $@{modelName}Repository
	)
	{
		\$this->@{modelName}Action = $@{modelName}Action;
		\$this->@{modelName}Service = $@{modelName}Service;
		\$this->@{modelName}Repository = $@{modelName}Repository;
	}
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request \$request)
    {
        @{index}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        @{create}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        @{store}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function show(\$id)
    {
        @{show}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function edit(\$id)
    {
        @{edit}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, \$id)
    {
        @{update}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function destroy(\$id)
    {
        @{destroy}
    }
}

CODE;

	}
	/**
	 * Function: common
	 * Notes: 通用模板
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  11:13
	 * @return string
	 */
	protected static function commonWithoutModel()
	{
		return <<<CODE
<?php

namespace App\Http\Controllers@{namespace};

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class @{controllerName} extends Controller
{
	# CUD 操作
	# R 操作
	# service 核心操作
	
	# 注入依赖
	public function __construct()
	{
	}
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request \$request)
    {
        @{index}
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        @{create}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @return \Illuminate\Http\Response
     */
    public function store(Request \$request)
    {
        @{store}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function show(\$id)
    {
        @{show}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function edit(\$id)
    {
        @{edit}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  \$request
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function update(Request \$request, \$id)
    {
        @{update}
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  \$id
     * @return \Illuminate\Http\Response
     */
    public function destroy(\$id)
    {
        @{destroy}
    }
}

CODE;

	}
	
	/**
	 * Function: index
	 * Notes: index 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  11:23
	 * @return string
	 */
	protected static function index()
	{
		return <<<CODE
\$pageSize = \$request->get('pageSize') ?? 15; // 默认每页15
		\$page = \$request->get('page') ?? 1; // 默认第一页
		
		// 筛选条件
		\$where = [];
		
		// 所需字段
		\$select = ['*'];
		
		// 数据
		\$data = \$this->@{modelName}Repository->getListPlusTwo(\$pageSize, \$page, \$where, \$select);
		
		return \$this->api()->success(\$data);
CODE;

	}
	
	/**
	 * Function: create
	 * Notes: create 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:14
	 */
	protected static function create()
	{
		return <<<CODE
// mvvm 很少使用 mvc常用
		// return view('');
CODE;
	}
	
	/**
	 * Function: store
	 * Notes: store 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:14
	 */
	protected static function store()
	{
		return <<<CODE
\$needFields = []; // 所需字段
		\$data = \$request->only(\$needFields);
		
		// 储存
		\$res = \$this->@{modelName}Action->create(\$data);
		
		if (!\$res) {
			return \$this->api()->fail();
		}
		
		return \$this->api()->success();
CODE;

	}
	
	/**
	 * Function: show
	 * Notes: show 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:14
	 */
	protected static function show()
	{
		return <<<CODE
\$where = [];
		\$where[] = ['id', \$id];
		\$select = ['*'];
		
		\$data = \$this->@{modelName}Repository->first(\$where, \$select);
		
		if (!\$data) {
			return \$this->api()->notFound();
		}
		
		return \$this->api()->success(\$data);

CODE;

	}
	
	/**
	 * Function: edit
	 * Notes: edit 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:14
	 */
	protected static function edit()
	{
		return <<<CODE
// mvvm 很少使用 mvc常用
		// return view('');
CODE;
	}
	
	/**
	 * Function: update
	 * Notes: update 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:14
	 */
	protected static function update()
	{
		return <<<CODE
\$needFields = []; // 所需字段
		\$data = \$request->only(\$needFields);
		
		// 检测是否存在
		\$res = \$this->@{modelName}Repository->first(['id' => \$id]);
		if (!\$res) {
			return \$this->api()->notFound();
		}
		
		// 更新
		\$res = \$this->@{modelName}Action->update(['id' => \$id],\$data);
		
		if (!\$res) {
			return \$this->api()->fail();
		}
		
		return \$this->api()->success();
CODE;

	}
	
	/**
	 * Function: destroy
	 * Notes: destroy 复杂模型注入
	 * User: Joker
	 * Email: <jw.oz@outlook.com>
	 * Date: 2019-09-25  15:14
	 */
	protected static function destroy()
	{
		return <<<CODE
// 检测是否存在
		\$res = \$this->@{modelName}Repository->first(['id' => \$id]);
		if (!\$res) {
			return \$this->api()->notFound();
		}
		
		// 执行删除
		\$res = \$this->@{modelName}Action->destroy(['id' => \$id]);
		
		if (!\$res) {
			return \$this->api()->fail();
		}
		
		return \$this->api()->success();
CODE;

	}
}