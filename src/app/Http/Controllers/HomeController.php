<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ChatGPTServiceInterface;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    private ChatGPTServiceInterface $chatGPTService;

    public function __construct(ChatGPTServiceInterface $chatGPTService)
    {
        $this->chatGPTService = $chatGPTService;
    }

    public function index()
    {
        return view('home');
    }

    public function submit(Request $request)
    {
        try {
            $response = $this->chatGPTService->sendMessage($request->input('text'));
            return back()->with('message', 'Відповідь від ChatGPT: ' . $response);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
