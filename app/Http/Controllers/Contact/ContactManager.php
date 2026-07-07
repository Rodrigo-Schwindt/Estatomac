<?php

namespace App\Http\Controllers\Contact;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\Banner;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class ContactManager extends Controller
{
    public function index()
    {
        $contact = Contact::first();
        $banner = Banner::forSection('contacto')->first();

        $previews = [
            'banner_image' => $banner?->image_banner 
                ? Storage::url($banner->image_banner) 
                : null,
        ];

        return view('livewire.contact.contact-manager', [
            'contact' => $contact,
            'banner' => $banner,
            'previews' => $previews
        ]);
    }

    public function save(Request $request)
    {
        try {
            $contact = Contact::first();

            if (!$contact) {
                $contact = new Contact();
            }

            $fields = ['direction_adm', 'direction_sale', 'phone_amd', 'maps_adm', 'frame_adm', 'mail_adm', 'wssp', 'facebook', 'insta', 'linkedin', 'youtube'];

            foreach ($fields as $field) {
                $contact->$field = $request->input($field);
            }

            $bannerData = [
                'title' => $request->input('banner_title', '')
            ];

            if ($request->hasFile('banner_image')) {
                $banner = Banner::where('section', 'contacto')->first();
                if ($banner && $banner->image_banner) {
                    Storage::disk('public')->delete($banner->image_banner);
                }
                $bannerData['image_banner'] = $request->file('banner_image')->store('banners/contacto', 'public');
            }

            if ($request->has('remove_banner_image')) {
                $banner = Banner::where('section', 'contacto')->first();
                if ($banner && $banner->image_banner) {
                    Storage::disk('public')->delete($banner->image_banner);
                    $bannerData['image_banner'] = null;
                }
            }

            Banner::updateOrCreate(['section' => 'contacto'], $bannerData);

            foreach ([1, 2, 3] as $i) {
                $temp = "icono_{$i}_temp";
                if ($request->hasFile($temp)) {
                    if ($contact->{"icono_$i"}) {
                        Storage::disk('public')->delete($contact->{"icono_$i"});
                    }
                    $contact->{"icono_$i"} = $request->file($temp)->store('contact', 'public');
                }
                if ($request->has("remove_icono_$i")) {
                    if ($contact->{"icono_$i"}) {
                        Storage::disk('public')->delete($contact->{"icono_$i"});
                    }
                    $contact->{"icono_$i"} = null;
                }
            }

            $contact->save();

            return response()->json(['success' => true, 'message' => 'Datos guardados correctamente']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}