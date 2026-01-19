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
        $validated = $request->validate([
            'direction_adm' => 'nullable|string|max:255',
            'direction_sale' => 'nullable|string|max:255',
            'phone_amd'     => 'nullable|string|max:50',
            'maps_adm'      => 'nullable|string',
            'frame_adm'     => 'nullable|string',
            'mail_adm'      => 'nullable|string|max:255',
            'wssp'          => 'nullable|string|max:255',
            'facebook'      => 'nullable|string|max:255',
            'insta'         => 'nullable|string|max:255',
            'linkedin'      => 'nullable|string|max:255',
            'youtube'       => 'nullable|string|max:255',
            'banner_title'  => 'nullable|string|max:255',
            'banner_image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'icono_1_temp'  => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:4096',
            'icono_2_temp'  => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:4096',
            'icono_3_temp'  => 'nullable|mimes:jpg,jpeg,png,webp,gif,svg|max:4096',
        ]);
    
        $contact = Contact::first();
    
        if (!$contact) {
            $contact = new Contact();
        }
    
        $contact->fill($validated);
    
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
    
        Banner::updateOrCreate(
            ['section' => 'contacto'],
            $bannerData
        );
    
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

        if ($contact->mail_adm) {
            $mailData = [
                'nombre' => 'Admin (Actualización de datos)',
                'empresa' => 'Sistema Interno',
                'email' => $contact->mail_adm,
                'celular' => $contact->wssp,
                'mensaje' => 'Se han actualizado los datos de contacto en el panel administrativo.'
            ];

            Mail::to($contact->mail_adm)->send(new ContactMail($mailData));
        }
    
        return redirect()->back()->with('toast', [
            'message' => 'Datos guardados y correo enviado correctamente',
            'type' => 'success'
        ]);
    }
}