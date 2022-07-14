<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Ticket\Ticket;
use Carbon\Carbon;

use RealRashid\SweetAlert\Facades\Alert;


class TicketController extends Controller
{
    //
    protected function getTickets($startDate = null, $endDate = null){
        $condition = [
            ['status', $this->unread]
        ];
        //if the start date and end date are not null add the
        if($startDate !== null && $endDate !== null){
            $condition[] = ['created_at', '>=', $startDate];
            $condition[] = ['created_at', '<', $endDate];
            $condition[] = ['status', $this->unread];
        }

        if($startDate !== null){
            $condition[] = ['status', $startDate];
        }

        $payload = [
            'tickets' => Ticket::where($condition)->orderBy('id', 'desc')->paginate($this->paginate),
        ];
        return view('pages.tickets.tickets', $payload);
    }
    protected function getTicketByDate(Request $request){
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('tickets/interface/'.$startDate.'/'.$endDate);
    }
    protected function getTicketByType(Request $request){
        $type = $request->user_type;
        return redirect()->to('tickets/interface/'.$type);
    } 

    protected function createTicketByUser(Request $request){
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'message'  => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->messages());
  
        $ticket = Ticket::create([
            'unique_id' => $this->createUniqueId('tickets'),
            'user_id' => $user->unique_id,
            'message' => $request->message,
            'file' => $this->uploadImageHandler($request, 'thumbnail', 'ticket_file', 'default.png'),
        ]);

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('ticket_sent'), $ticket);
    }

    protected function fectchUsersTicket($user_id = null){
        if($user_id == null){
            $user = $this->user()->unique_id;
        }else{
            $user = $user_id;
        }

        $ticket = Ticket::where('user_id', $user)->orderBy('id', 'desc')->get();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Ticket'), $ticket);
    }

    protected function markAsRead($unique_id = null){
        if($unique_id == null){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }

        $ticket = Ticket::where('unique_id', $unique_id)->first();
        if($ticket == null){
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Ticket'));
            return redirect()->back();
        }
        $ticket->update([
            'admin_id' => $this->user()->unique_id,
            'status' => $this->read,
        ]);
        Alert::success('Success', $this->returnSuccessMessage('updated', 'Ticket Status'));
        return redirect()->back();
    }

    protected function replyTicketInterface($unique_id = null){
        if($unique_id == null){
            Alert::error('Error', $this->returnErrorMessage('unknown_error'));
            return redirect()->back();
        }

        $ticket = Ticket::where('unique_id', $unique_id)->first();
        $ticket->update([
            'status' => $this->read,
        ]);
        $payload = [
            'tickets' => Ticket::where('user_id', $ticket->user_id)->get(),
            'unique_id' => $unique_id,
        ];
        return view('pages.tickets.reply-ticket', $payload);
    }

    protected function replyTicket(Request $request){
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'message'  => 'required',
        ]);
        if($validator->fails()){
            Alert::error('Error', $validator->messages()->first());
            return redirect()->back();
        }

        $ticket = Ticket::where('unique_id', $request->unique_id)->first();
        Ticket::create([
            'unique_id' => $this->createUniqueId('tickets'),
            'admin_id' => $user->unique_id,
            'user_id' => $ticket->user_id,
            'message' => $request->message,
            'send_status' => 'admin',
        ]);
        return redirect()->back();
    }
}
