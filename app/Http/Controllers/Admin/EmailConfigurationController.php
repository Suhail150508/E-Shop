<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\SettingService;
use Illuminate\Http\Request;

class EmailConfigurationController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display email configuration settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = $this->settingService->all();

        return view('admin.email-configuration.index', compact('settings'));
    }

    /**
     * Update email configuration settings.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|numeric',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string',
        ]);

        $data = $request->only([
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ]);

        foreach ($data as $key => $value) {
            $this->settingService->set($key, $value);
        }

        return redirect()->back()->with('success', __('Email configuration updated successfully.'));
    }

    /**
     * Display a listing of email templates.
     *
     * @return \Illuminate\View\View
     */
    public function templates()
    {
        // Auto-seed if empty (for convenience in this task)
        if (EmailTemplate::count() === 0) {
            $this->seedTemplates();
        }

        $templates = EmailTemplate::latest()->paginate(10);

        return view('admin.email-configuration.templates.index', compact('templates'));
    }

    /**
     * Show the form for editing the specified email template.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function editTemplate($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return view('admin.email-configuration.templates.edit', compact('template'));
    }

    /**
     * Update the specified email template in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateTemplate(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $template->update([
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return redirect()->route('admin.email-configuration.templates')->with('success', __('Email template updated successfully.'));
    }

    private function seedTemplates()
    {
        $templates = [
            [
                'name' => 'Welcome Email',
                'slug' => 'welcome_email',
                'subject' => 'Welcome to {{app_name}}',
                'message' => '<p>Hi {{name}},</p><p>Welcome to {{app_name}}! We are glad to have you.</p>',
            ],
            [
                'name' => 'Order Confirmation',
                'slug' => 'order_confirmation',
                'subject' => 'Order Confirmation #{{order_number}}',
                'message' => '<p>Hi {{name}},</p><p>Your order #{{order_number}} has been placed successfully.</p>',
            ],
            [
                'name' => 'Reset Password',
                'slug' => 'reset_password',
                'subject' => 'Reset Password Notification',
                'message' => '<p>Hello,</p><p>You are receiving this email because we received a password reset request for your account.</p>',
            ],
        ];

        foreach ($templates as $tmpl) {
            EmailTemplate::create($tmpl);
        }
    }
}
