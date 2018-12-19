<?php

namespace App\Http\Controllers\panel;

use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slider;
use App\Http\Requests\Poster;
use App\Http\Requests\Info;
use App\Http\Requests\SocialLink;
use App\Http\Requests\ShippingCost;
use Carbon\Carbon;
use App\Classes\jdf;
use App\Models\OrderProducts;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use App\Models\ProductVariation;

/**
 *  CLASS PanelController
 *  @
 * 
 * @author AmirKhadangi <AmirKhadangi920@gmail.com> 
 */
class PanelController extends Controller
{
    /**
     * Main page of panel dashboard
     *
     * @param string $total_type
     * @return View
     */
    public function index ($total_type = 'daily')
    {
        return view('panel.index', [
            'orders'        => Order::list(),
            'reviews'       => Review::list(),
            'top_products'  => ProductVariation::getTops(),
            'orders_count'  => Order::count(),
            'product_count' => Product::count(),
            'page_name'     => 'داشبورد',
            // 'total_income'  => Order::total_income(),
            'order_compare' => Order::compare(),
            'total_sales'   => Order::total($total_type),
            'total_type'    => $total_type,
            'options'       => $this->options(['site_name', 'site_logo', 'dollar_cost'])
        ]);
    }

    /**
     * return Setting panel
     *
     * @return View
     */
    public function setting ()
    {
        return view('panel.setting', [
            'page_name' => 'setting',
            'page_title' => 'تنظیمات',
            'options' => $this->options([
                'slider', 'posters', 'site_name', 'site_description', 'site_logo',
                'shop_phone', 'shop_address', 'social_link', 'shipping_cost'
            ])
        ]);
    }

    public function slider (Slider $req)
    {
        $option = Option::select('id', 'value')->where('name', 'slider')->get();
        $option_id = $option[0]->id;
        $option_value = json_decode($option[0]->value, true);
        $slider = $req->slides;
        
        foreach ($req->slides as $key => $item) 
        {
            if (isset($item['photo']))
            {
                $file_path = public_path().'/slider/'.$option_value[$key]['photo'];
                if(file_exists($file_path)) {
                    unlink($file_path);
                }

                $photoName = substr(md5(time()), 0, 8) .'.'.$item['photo']->getClientOriginalExtension();
                $item['photo']->move(public_path('slider'), $photoName);
                
                $slider[$key]['photo'] = $photoName;
            } else {
                $slider[$key]['photo'] = $option_value[$key]['photo'];
            }
        }
        
        $slider = json_encode($slider);
        
        $option = Option::find($option_id);
        $option -> value = $slider;
        $option -> save();

        return redirect()->back()->with('message', 'اسلایدر با موفقیت بروز رسانی شد');
    }

    public function poster (Poster $req)
    {
        $option = Option::select('id', 'value')->where('name', 'posters')->get();
        $option_id = $option[0]->id;
        $option_value = json_decode($option[0]->value, true);
        $posters = $req->posters;
    
        foreach ($req->posters as $key => $item) 
        {
            if (isset($item['photo']))
            {
                $file_path = public_path().'/poster/'.$option_value[$key]['photo'];
                if(file_exists($file_path)) {
                    unlink($file_path);
                }

                $photoName = substr(md5(time()), 0, 8) .'.'.$item['photo']->getClientOriginalExtension();
                $item['photo']->move(public_path('poster'), $photoName);
                
                $posters[$key]['photo'] = $photoName;
            } else {
                $posters[$key]['photo'] = $option_value[$key]['photo'];
            }
        }
        
        $posters = json_encode($posters);
        
        $option = Option::find($option_id);
        $option -> value = $posters;
        $option -> save();

        return redirect()->back()->with('message', 'پوستر ها با موفقیت بروز رسانی شدند');
    }

    public function info (Info $req)
    {
        $option = Option::select('id', 'name', 'value')->whereIn('name', [
            'site_name', 'site_description', 'site_logo', 'shop_phone', 'shop_address'
        ])->get();

        $options = [];
        foreach ($option as $item) {
            $options[$item['name']] = ['id' => $item['id'], 'value' => $item['value']];
        }
        $info = $req->all();
        
        if (isset($info['logo']))
        {
            $file_path = public_path().'/logo/'.$options['site_logo']['value'];
            if(file_exists($file_path)) {
                unlink($file_path);
            }

            $photoName = substr(md5(time()), 0, 8) .'.'.$info['logo']->getClientOriginalExtension();
            $info['logo']->move(public_path('logo'), $photoName);
            
            $options['site_logo']['value'] = $photoName;
        }
    
        $options['site_name']['value'] = $info['site_name'];
        $options['site_description']['value'] = $info['description'];
        $options['shop_phone']['value'] = $info['phone'];
        $options['shop_address']['value'] = $info['address'];
        
        foreach ($options as $item)
        {
            $option = Option::find($item['id']);
            $option -> value = $item['value'];
            $option -> save();
        }

        return redirect()->back()->with('message', 'اطلاعات کلی با موفقیت بروز رسانی شدند');
    }

    public function social_link (SocialLink $req)
    {
        $option = Option::select('id', 'value')->where('name', 'social_link')->get();
        $option_id = $option[0] -> id;
        $option_value = json_decode($option[0] -> value, true);

        $option_value['instagram'] = $req->instagram;
        $option_value['telegram'] = $req->telegram;
        $option_value['twitter'] = $req->twitter;
        $option_value['facebook'] = $req->facebook;

        $option = Option::find($option_id);
        $option -> value = json_encode($option_value);
        $option -> save();

        return redirect()->back()->with('message', 'لینک شبکه های اجتماعی با موفقیت بروز رسانی شدند');
    }

    public function dollar_cost ($dollar_cost)
    {
        $option = Option::select('id')->where('name', 'dollar_cost')->get();
        $option = Option::find($option[0]->id);
        $option -> value = $dollar_cost;
        $option -> save();
        
        return redirect()->back()->with('message', 'قیمت دلار با موفقیت بروز رسانی شد');
    }

    public function shipping_cost (ShippingCost $req)
    {
        $option = Option::select('id', 'value')->where('name', 'shipping_cost')->get();
        $option_id = $option[0] -> id;
        $option_value = json_decode($option[0] -> value, true);

        $option_value['model1']['cost'] = $req->shipping_cost['model1'];
        $option_value['model2']['cost'] = $req->shipping_cost['model2'];
        $option_value['model3']['cost'] = $req->shipping_cost['model3'];
        $option_value['model4']['cost'] = $req->shipping_cost['model4'];

        $option = Option::find($option_id);
        $option -> value = json_encode($option_value);
        $option -> save();

        return redirect()->back()->with('message', 'هزینه های ارسال با موفقیت بروز رسانی شدند');
    }
}