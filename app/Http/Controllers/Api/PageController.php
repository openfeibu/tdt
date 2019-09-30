<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Repositories\Eloquent\PageCategoryRepositoryInterface;
use App\Repositories\Eloquent\PageRepositoryInterface;
use App\Repositories\Eloquent\SettingRepositoryInterface;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends BaseController
{
    public function __construct(PageRepositoryInterface $page,
                                PageCategoryRepositoryInterface $category_repository,
                                SettingRepositoryInterface $setting_repository)
    {
        parent::__construct();
        $this->repository = $page;
        $this->category_repository = $category_repository;
        $this->setting_repository = $setting_repository;
        $this->repository
            ->pushCriteria(\App\Repositories\Criteria\PageResourceCriteria::class);
    }

    public function getPage(Request $request, $id)
    {
        $data = $this->repository
            ->setPresenter(\App\Repositories\Presenter\Api\PageShowPresenter::class)
            ->where(['status' => 'show'])
            ->find($id);
        if(!$data)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('数据不存在');
        }
        $page = $data['data'];
        return response()->json([
            'code' => '200',
            'meta_title' => $page['meta_title'],
            'meta_keyword' => $page['meta_keyword'],
            'meta_description' => $page['meta_description'],
            'data' => $page,
        ]);
    }
    public function getPageSlug(Request $request,$slug)
    {
        $page = $this->repository
            ->where(['status' => 'show','slug' => $slug])
            ->first(['title','content']);
        if(!$page)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('数据不存在');
        }
        $page = $page->toArray();
        $data = [
            //'content' => url('/page_html/slug/'.$slug),
            'content' => $page['content'],
            'title' => $page['title']
        ];
        return $this->response->success()->data($data)->json();
    }
    public function getPageHtmlSlug(Request $request,$slug)
    {
        $data = $this->repository
            ->where(['status' => 'show','slug' => $slug])
            ->first(['title','content']);
        if(!$data)
        {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('数据不存在');
        }
        $page = $data->toArray();

        return $this->response->title($page['title'])
            ->view('page')
            ->data(compact('page'))
            ->output();

    }

}
