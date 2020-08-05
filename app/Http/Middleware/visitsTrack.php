<?php

namespace App\Http\Middleware;

use App\daily_visit;
use Closure;
use Jenssegers\Agent\Agent;

class visitsTrack{
    /**
     * Handle an incoming request.
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */

    public function handle($request, Closure $next){


        $agent = new Agent();
        $route = $request->url();
        $user_ip = $request->ip();
        $user_browser = $agent->browser();
        $user_platform = $agent->platform();
        $is_mobile = $agent->isMobile();
        $is_robot = $agent->isRobot();
        $visited_date = date('d-m-Y');

        $d = new \DateTime();
        $dd = $d->format('Y-m-d 00:00:00');

        $view_tracker_count = \App\viewTracker::where('user_ip', $user_ip)->where('visited_date', $visited_date)->count();
        $today_count = daily_visit::where('created_at', '=', $dd)->count();
        if($view_tracker_count == 0 && $is_robot == 0){

            if($today_count == 0){
                $t = new daily_visit();
                $t->count = 1;
                $t->created_at = $dd;
                $t->updated_at = $dd;
                $t->save();
            } else {
                $today_count_id = daily_visit::where('created_at', $dd)->value('id');
                $t = daily_visit::find($today_count_id);
                $t->count = $t->count + 1;
                $t->save();
            }

        }


        $view_tracker_count = \App\viewTracker::where('user_ip', $user_ip)->where('user_platform', $user_platform)->where('user_browser', $user_browser)->where('visited_date', $visited_date)->where('route', $route);
        if($view_tracker_count->count() != 0){
            $view_tracker = \App\viewTracker::find($view_tracker_count->value('id'));
            $view_tracker->count = $view_tracker->count + 1;
            $view_tracker->save();
        } else {
            $view_tracker = new \App\viewTracker();
            $view_tracker->user_ip = $user_ip;
            $view_tracker->user_browser = $user_browser;
            $view_tracker->user_platform = $user_platform;
            $view_tracker->is_mobile = $is_mobile;
            $view_tracker->is_robot = $is_robot;
            $view_tracker->visited_date = $visited_date;
            $view_tracker->route = $route;
            $view_tracker->referral_route = url()->previous();
            $view_tracker->count = 1;
            $view_tracker->save();
        }

        return $next($request);
    }
}
