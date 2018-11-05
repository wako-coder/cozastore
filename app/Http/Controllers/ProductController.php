<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateProduct;
use App\Http\Requests\AddReview;
use Illuminate\Support\Facades\DB;
use Cookie;
use App\Group;
use App\Feature;
use App\Option;
use App\Product;
use App\ProductFeatures;
use App\Review;
use App\Gallery;

class ProductController extends Controller
{
    public function index ()
    {
        $products = Product::select('pro_id', 'name', 'code', 'price', 'unit',  'offer', 'status', 'photo')
            ->orderBy('created_at', 'DESC')->get();

        $options = Option::select('name', 'value')->whereIn('name', ['site_name', 'site_logo'])->get();
        foreach ($options as $option) {
            switch ($option['name']) {
                case 'site_name': $site_name = $option['value']; break;
                case 'site_logo': $site_logo = $option['value']; break;
            }
        }

        return view('panel.products', [
            'products' => $products,
            'page_name' => 'products',
            'page_title' => 'محصولات',
            'site_name'=> $site_name,
            'site_logo'=> $site_logo
        ]);
    }
    
    public function add ()
    {
        $groups = Group::select('id', 'title', 'description')->where('parent', null)->get();

        $features = Feature::select('id', 'name')->where('title', null)->get();
        foreach ($features as $feature) {
            $feature->subs = Feature::select('id', 'name')->where('title', $feature->id)->get();
        }

        $photos = Gallery::select('id', 'name', 'description', 'photo')->skip(0)->take(30)->get();

        $options = Option::select('name', 'value')->whereIn('name', ['site_name', 'site_logo'])->get();
        foreach ($options as $option) {
            switch ($option['name']) {
                case 'site_name': $site_name = $option['value']; break;
                case 'site_logo': $site_logo = $option['value']; break;
            }
        }

        return view('panel.add-product', [
            'groups' => $groups,
            'features' => $features,
            'photos' => $photos,
            'page_name' => 'add_product',
            'page_title' => 'ثبت محصول',
            'site_name'=> $site_name,
            'site_logo'=> $site_logo
        ]);
    }

    public function create (CreateProduct $req)
    {
        $req->aparat_video = substr($req->aparat_video, strripos($req->aparat_video, '/') + 1);
        // Get a random 8 chars name for this product
        $pro_id = substr(md5(time()), 0, 8);

        // Insert product details to database
        $product = new Product();
        $product->pro_id = $pro_id;
        if ($req -> parent) {
            $temp = Group::select('parent')->where('id', $req->parent)->get();
            while (!empty($temp[0])) {
                $temp = Group::select('parent')->where('id', $temp[0]->parent)->get();
                if (isset($temp[0]) && $temp[0]->parent !== null) {
                    $parent_category = $temp[0]->parent;
                }
            }   
            $product->parent_category = $parent_category; 
        }
        $product->category = $req -> parent;
        $product->name = $req -> name;
        $product->code = $req -> code;
        $product->short_description = $req -> short_description;
        $product->aparat_video = $req -> aparat_video;
        $product->price = $req -> price;
        $product->unit = $req -> unit;
        $product->offer = ($req->offer == null)? 0 : $req->offer;
        $product->colors = $req -> colors;
        $product->status = $req -> status;
        $product->full_description = $req -> full_description;
        $product->keywords = $req -> keywords;
        $product->photo = $req -> photo;
        $product->gallery = $req -> gallery;
        $product->advantages = $req -> advantages;
        $product->disadvantages = $req -> disadvantages;

        $product -> save();

        // Add all product features to it's table
        foreach ($req ->features as $item) {

            if ($item['name'] != 'false') {
                $product_feature = new ProductFeatures;
                $product_feature -> product = $pro_id;
                $product_feature -> feature = $item['name'];
                $product_feature -> value = $item['value'];
                $product_feature -> save();
            }
        }

        return redirect()->back()->with('message', 'محصول '.$req->name.' با موفقیت ثبت شد .');
    }

    public function edit ($id)
    {
        $product = DB::select("SELECT `pro_id`, `category`, `categories`.`title`, `products`.`name`, `code`,
            `short_description`, `aparat_video`, `price`, `unit`, `offer`, `colors`, `status`,
            `full_description`, `keywords`, `photo`, `gallery`, `advantages`, `disadvantages` 
            FROM `products`
            LEFT JOIN `categories` ON `products`.`category` = `categories`.`id` WHERE `pro_id` = ?", [$id]);

        $photos = Gallery::select('id', 'name', 'description', 'photo')
                ->whereNotIn('photo', explode(',', $product[0]->gallery))->skip(0)->take(30)->get();
            
        $product_feature = ProductFeatures::select('feature', 'value')->where('product', $id)->get();
        
        $groups = Group::select('id', 'title', 'description')->where('parent', null)->get();

        $features = Feature::select('id', 'name')->where('title', null)->get();
        foreach ($features as $feature) {
            $feature->subs = Feature::select('id', 'name')->where('title', $feature->id)->get();
        }

        $options = Option::select('name', 'value')->whereIn('name', ['site_name', 'site_logo'])->get();
        foreach ($options as $option) {
            switch ($option['name']) {
                case 'site_name': $site_name = $option['value']; break;
                case 'site_logo': $site_logo = $option['value']; break;
            }
        }

        return view('panel.add-product', [
            'groups' => $groups,
            'features' => $features,
            'product' => $product[0],
            'photos' => $photos,
            'product_features' => $product_feature,
            'edit' => true,
            'page_name' => 'products',
            'page_title' => 'ویرایش محصول ' . $product[0]->name,
            'site_name'=> $site_name,
            'site_logo'=> $site_logo
        ]);
    }

    public function update (CreateProduct $req)
    {
        $req->aparat_video = substr($req->aparat_video, strripos($req->aparat_video, '/') + 1);

        // Insert product details to database
        $product = Product::find($req -> id);
        if ($req -> parent) {
            $temp = Group::select('parent')->where('id', $req->parent)->get();
            while (!empty($temp[0])) {
                $temp = Group::select('parent')->where('id', $temp[0]->parent)->get();
                if (isset($temp[0]) && $temp[0]->parent !== null) {
                    $parent_category = $temp[0]->parent;
                }
            }   
            $product->parent_category = $parent_category; 
        }
        $product->category = $req -> parent;
        $product->name = $req -> name;
        $product->code = $req -> code;
        $product->short_description = $req -> short_description;
        $product->aparat_video = $req -> aparat_video;
        $product->price = $req -> price;
        $product->unit = $req -> unit;
        $product->offer = ($req->offer == null)? 0 : $req->offer;
        $product->colors = $req -> colors;
        $product->status = $req -> status;
        $product->full_description = $req -> full_description;
        $product->keywords = $req -> keywords;
        $product->photo = $req -> photo;
        $product->gallery = $req -> gallery;
        $product->advantages = $req -> advantages;
        $product->disadvantages = $req -> disadvantages;

        $product -> save();


        // Removes all old featuers
        DB::delete("DELETE FROM `product_features` WHERE `product` = ?", [$req->id]);

        // Add all product features to it's table
        foreach ($req ->features as $item) {

            if ($item['name'] != 'false') {
                $product_feature = new ProductFeatures;
                $product_feature -> product = $req -> id;
                $product_feature -> feature = $item['name'];
                $product_feature -> value = $item['value'];
                $product_feature -> save();
            }
        }

        return redirect()->back()->with('message', 'محصول '.$req->name.' با موفقیت بروزرسانی شد .');
    }

    public function delete ($id, $title)
    {
        Product::destroy($id);
        return redirect()->back()->with('message', 'محصول '.$title.' با موفقیت حذف شد .');
    }

    public function search ($query)
    {
        $products = Product::select('pro_id', 'name', 'code', 'price', 'unit',  'offer', 'status', 'photo')
            ->orderBy('created_at', 'DESC')->where('name', 'like', '%'.$query.'%')->get();

        $options = Option::select('name', 'value')->whereIn('name', ['site_name', 'site_logo'])->get();
        foreach ($options as $option) {
            switch ($option['name']) {
                case 'site_name': $site_name = $option['value']; break;
                case 'site_logo': $site_logo = $option['value']; break;
            }
        }

        return view('panel.products', [
            'products', $products,
            'query' => $query,
            'page_name' => 'products',
            'page_title' => 'جستجوی محصولات برای "' . $query . '"',
            'site_name'=> $site_name,
            'site_logo'=> $site_logo
        ]);
    }

    public function breadcrumb ($id)
    {
        function get_parents (&$output, $p, $i = 0) {
            $sql = "SELECT `cat1`.`parent`, `cat1`.`id`, `cat1`.`title` FROM `categories` as `cat1`
                INNER JOIN `categories` as `cat2` ON `cat1`.`id` = `cat2`.`parent` WHERE `cat2`.`id` = ?";
            
            $output[$i] = DB::select($sql, [$p]);

            if (!empty($output[$i][0]->parent)) {
                get_parents($output, $output[$i][0]->id, ++$i);
            }
        }

        $results = [];
        get_parents($results, $id);
        return $results;
    }
}
