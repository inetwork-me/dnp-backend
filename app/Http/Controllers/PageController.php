<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionTranslation;
use App\Models\PageTranslation;


class PageController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:add_website_page'])->only('create');
        $this->middleware(['permission:edit_website_page'])->only('edit');
        $this->middleware(['permission:delete_website_page'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.website_settings.pages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $page = new Page;
        $page->title = $request->title;

        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));
        if (Page::where('slug', $slug)->first() == null) {
            $page->slug = $slug;
            $page->type = "custom_page";
            $page->content = $request->content;
            $page->meta_title = $request->meta_title;
            $page->meta_description = $request->meta_description;
            $page->keywords = $request->keywords;
            $page->meta_image = $request->meta_image;
            $page->save(); // Save the page to get the ID

            // Save page translation
            $page_translation = PageTranslation::firstOrNew([
                'lang' => env('DEFAULT_LANGUAGE'),
                'page_id' => $page->id
            ]);
            $page_translation->title = $request->title;
            $page_translation->content = $request->content;
            $page_translation->save();

           
            flash(translate('New page has been created successfully'))->success();
            return redirect()->route('website.pages');
        }

        flash(translate('Slug has been used already'))->warning();
        return back();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $page_name = $request->page;
        $page = Page::where('slug', $id)->first();
        $page_sections = PageSection::where('page_id', $page->id)->where('lang', $lang)->get();
        if ($page != null) {
            if ($page_name == 'home') {
                return view('backend.website_settings.pages.' . get_setting('homepage_select') . '.home_page_edit', compact('page', 'lang', 'page_sections'));
            }
            return view('backend.website_settings.pages.edit', compact('page', 'lang', 'page_sections'));
        }
        abort(404);
    }

    public function sections(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $lang = isset($request->lang) ? $request->lang : env('DEFAULT_LANGUAGE');
        if ($page != null) {
            $page_sections = PageSection::where('page_id', $page->id)->where('lang', $lang)->get();
            return view('backend.website_settings.pages.sections.index', compact('page','lang', 'page_sections'));
        }
        abort(404);
    }
    
    public function sectionCreate(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $lang = isset($request->lang) ? $request->lang : env('DEFAULT_LANGUAGE');
        if ($page != null) {
            return view('backend.website_settings.pages.sections.create', compact('page','lang'));
        }
        abort(404);
    }

    public function sectionStore(Request $request, $id)
    {
        PageSection::create([
            'title' => $request->title,
            'content' => $request->content,
            'image' => $request->image,
            'lang' => $request->lang,
            'page_id' => $id
        ]);
        flash(translate('Section has been created successfully'))->success();
        return back();
    }
    
    public function sectionDelete($id)
    {
        $section = PageSection::findOrFail($id);
        if ($section->delete()) {
            flash(translate('Section has been deleted successfully'))->success();
            return redirect()->back();
        }
        return back();
    }

    public function sectionUpdate(Request $request, $id)
    {
        $page = PageSection::findOrFail($id);
        if ($request->has('sections')) {
            $sectionsData = $request->input('sections');
            foreach ($sectionsData as $index => $section) {
                $page->update([
                    'title' => $section['title'] ?? null,
                    'content' => $section['content'] ?? null,
                    'image' => $section['image'] ?? null,
                    'lang' => $request->lang,
                ]);
            }
        }
        flash(translate('Section has been updated successfully'))->success();
        return back();
    }

    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->slug));

        if (Page::where('id', '!=', $id)->where('slug', $slug)->first() == null) {
            if ($page->type == 'custom_page') {
                $page->slug = $slug;
            }

            if ($request->lang == env("DEFAULT_LANGUAGE")) {
                $page->title = $request->title;
                $page->content = $request->content;
            }

            $page->meta_title = $request->meta_title;
            $page->meta_description = $request->meta_description;
            $page->keywords = $request->keywords;
            $page->meta_image = $request->meta_image;
            $page->save();

            $page_translation = PageTranslation::firstOrNew(['lang' => $request->lang, 'page_id' => $page->id]);
            $page_translation->title = $request->title;
            $page_translation->content = $request->content;
            $page_translation->save();


            flash(translate('Page has been updated successfully'))->success();
            return redirect()->route('website.pages');
        }

        flash(translate('Slug has been used already'))->warning();
        return back();
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->page_translations()->delete();

        if (Page::destroy($id)) {
            flash(translate('Page has been deleted successfully'))->success();
            return redirect()->back();
        }
        return back();
    }

    public function show_custom_page($slug)
    {

        $page = Page::where('slug', $slug)->first();
        if ($page != null) {
            return view('frontend.custom_page', compact('page'));
        }
        abort(404);
    }
    public function mobile_custom_page($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if ($page != null) {
            return view('frontend.m_custom_page', compact('page'));
        }
        abort(404);
    }
}
