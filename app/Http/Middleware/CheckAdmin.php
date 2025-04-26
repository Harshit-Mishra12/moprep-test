<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Session;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){

        $url = $request->path();
		if(\Auth::user()->user_type != 'Admin'){
            return redirect('/');
        }
      
        $menu = \App\Models\Menu::select('id','label','parent')->where('active_link',$url)->first();
        

        $designation = \App\Models\Designation::select('permissions')->where('id',\Auth::user()->designation_id)->first();
        $permission = json_decode($designation->permissions, true);
                //   dd($permission);
        $parent_id = $menu && $menu->parent != null ? $menu->parent :  0;
           
        $permission = isset($permission[$parent_id]) ? $permission[$parent_id] : null;
            // dd($permission);
     
            

            if(!empty($permission)){
                if(isset($permission) && $permission['read'] == 'true'){
                    return $next($request);
                }
                else{
                    return redirect('/');
                }
            }

        if (strpos($url, 'add') !== false) {
       
            

            if(!empty($permission)){
                if(isset($permission) && $permission['write'] == 'true'){
                    return $next($request);
                }
                else{
                    return redirect('/');
                }
            }
        } 

        if (strpos($url, 'update') !== false) {
            
           
            // dd($permission);

            if(!empty($permission)){
                if(isset($permission) && $permission['edit'] == 'true'){
                    return $next($request);
                }
                else{
                    return redirect('/');
                }
            }
        } 
        


		


		// if(\Auth::user()->user_type != 'Admin'){
            
        //     return redirect('/');
        // }
		
        return $next($request);
// 			dd($permissions);
        return $next($request);
    }
}
