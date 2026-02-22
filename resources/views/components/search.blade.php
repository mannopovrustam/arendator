<div class="text-base w-full :text-white">

    <div class="relative flex justify-center xl:flex mb-11"
         data-tw-toggle="modal" data-tw-target="#quick-search">
        <div
            class="flex cursor-pointer w-3/4 items-center rounded-[0.5rem] border border-transparent bg-white/[0.12] px-3.5 py-4 text-white/60 transition-colors duration-300 hover:bg-white/[0.15] hover:duration-100">
            <i data-tw-merge data-lucide="search"
               class="stroke-[1] h-[18px] w-[18px]"></i>
            <div class="ml-2.5 mr-auto">Dorilar. Xizmatlar. Shifokorlarni qidirish...</div>
            <div>⌘K</div>
        </div>
    </div>
    <div id="quick-search" aria-hidden="true" tabindex="-1"
         class="modal group bg-gradient-to-b from-theme-1/50 via-theme-2/50 to-black/50 transition-[visibility,opacity] w-screen h-screen fixed left-0 top-0 overflow-y-hidden z-[60] [&:not(.show)]:duration-[0s,0.2s] [&:not(.show)]:delay-[0.2s,0s] [&:not(.show)]:invisible [&:not(.show)]:opacity-0 [&.show]:visible [&.show]:opacity-100 [&.show]:duration-[0s,0.1s]">
        <div
            class="relative mx-auto my-2 w-[95%] scale-95 transition-transform group-[.show]:scale-100 sm:mt-40 sm:w-[600px] lg:w-[700px]">
            <div class="relative">
                <div
                    class="absolute inset-y-0 left-0 flex w-12 items-center justify-center">
                    <i data-tw-merge data-lucide="search"
                       class="stroke-[1] w-5 h-5 -mr-1.5 text-slate-500"></i>
                </div>
                <input data-tw-merge type="text"
                       placeholder="Dorilar. Xizmatlar. Shifokorlarni qidirish..."
                       id="search_term"
                       class="disabled:bg-slate-100 disabled:cursor-not-allowed dark:disabled:bg-darkmode-800/50 dark:disabled:border-transparent [&[readonly]]:bg-slate-100 [&[readonly]]:cursor-not-allowed [&[readonly]]:dark:bg-darkmode-800/50 [&[readonly]]:dark:border-transparent transition duration-200 ease-in-out w-full border-slate-200 placeholder:text-slate-400/90 focus:ring-primary focus:ring-opacity-20 focus:border-primary focus:border-opacity-40 dark:bg-darkmode-800 dark:border-transparent dark:focus:ring-slate-700 dark:focus:ring-opacity-50 dark:placeholder:text-slate-500/80 [&[type='file']]:border file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:border-r-[1px] file:border-slate-100/10 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-500/70 hover:file:bg-200 group-[.form-inline]:flex-1 group-[.input-group]:rounded-none group-[.input-group]:[&:not(:first-child)]:border-l-transparent group-[.input-group]:first:rounded-l group-[.input-group]:last:rounded-r group-[.input-group]:z-10 rounded-lg border-0 py-3.5 pl-12 pr-14 text-base shadow-lg focus:ring-0">
                <div
                    class="absolute inset-y-0 right-0 flex w-14 items-center">
                    <div
                        class="mr-auto rounded-[0.4rem] border bg-slate-100 px-2 py-1 text-xs text-slate-500/80">
                        ESC
                    </div>
                </div>
            </div>
            <div
                class="global-search global-search--show-result group relative z-10 mt-1 max-h-[468px] overflow-y-auto rounded-lg bg-white pb-1 shadow-lg sm:max-h-[615px]"
                id="search_result">
            </div>
        </div>
    </div>


    <div>
        <div class="mt-10 text-left"
             style="font-size: 48px; color: #fff; font-family: Ruda, sans-serif; margin-bottom: 50px;">
        </div>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    $(document).on('keyup', '#search_term', function () {
        var query = $(this).val();
        if (query != '') {
            var _token = $('input[name="_token"]').val();
            $.ajax({
                url: "/search",
                method: "GET",
                data: {
                    term: query,
                    type: 'all',
                    _token: _token
                },
                success: function (data) {
                    $('#search_result').show();
                    var html = ``;
                    if (data.length == 0) {
                        html += `<div
                                    class="flex flex-col items-center justify-center pb-28 pt-20 group-[.global-search--show-result]:hidden">
                                    <i data-tw-merge data-lucide="search-x"
                                       class="h-20 w-20 fill-theme-1/5 stroke-[0.5] text-theme-1/20"></i>
                                    <div class="mt-5 text-xl font-medium">
                                        No result found
                                    </div>
                                    <div
                                        class="mt-3 w-2/3 text-center leading-relaxed text-slate-500">
                                        No results found for
                                        <span
                                            class="global-search__keyword font-medium italic"></span>
                                        . Please try a different search term or check your
                                        spelling.
                                    </div>
                                </div>`
                    }
                    $.each(data, function (idx, values) {
                        if (idx == 'drug' && values.length > 0) {
                            html += `<div class="border-t border-dashed px-5 py-4">
                            <div class="flex items-center">
                                <div class="text-xs uppercase text-slate-500">
                                    Dorilar
                                </div>
                                <form action="/search" method="GET" class="ml-auto text-xs text-slate-500">
                                    @csrf
                                 <input type="hidden" name="type" value="drugs">
                                 <input type="hidden" name="term" value="${query}">
                                 <button type="submit">
                                     Barchasini ko'rish
                                 </button>
                                 </form>
                            </div>
                            <div class="mt-3.5 flex flex-col gap-1">`;
                            $.each(values, function (index, value) {
                                html += `<a class="flex items-center gap-2.5 rounded-md border border-transparent p-1 hover:border-slate-100 hover:bg-slate-50/80" href="/drugs/` + value.id + `">
                                                <div class="truncate font-medium">` + value.name + `</div>
                                            </a>`;
                            })
                            html += `</div></div>`;
                        }
                        if (idx == 'person' && values.length > 0) {
                            html += `<div class="border-t border-dashed px-5 py-4">
                            <div class="flex items-center">
                                <div class="text-xs uppercase text-slate-500">
                                    Shaxslar
                                </div>
                                <form action="/persons" method="GET" class="ml-auto text-xs text-slate-500">
                                    @csrf
                            <input type="hidden" name="type" value="persons">
                            <input type="hidden" name="term" value="${query}">
                            <button type="submit">
                                Barchasini ko'rish
                            </button>
                        </form>
                                                    </div>
                                                    <div class="mt-3.5 flex flex-col gap-1">`;
                            $.each(values, function (index, value) {
                                html += `<a class="flex items-center gap-2.5 rounded-md border border-transparent p-1 hover:border-slate-100 hover:bg-slate-50/80" href="/persons/` + value.slug + `">
                                                <div class="truncate font-medium">` + value.name + `</div>
                                            </a>`;
                            })
                            html += `</div></div>`;
                        }

                        if (idx == 'organization' && values.length > 0) {
                            html += `<div class="border-t border-dashed px-5 py-4">
                            <div class="flex items-center">
                                <div class="text-xs uppercase text-slate-500">
                                    Tashkilotlar
                                </div>
                                <form action="/search" method="GET" class="ml-auto text-xs text-slate-500">
                                    @csrf
                            <input type="hidden" name="type" value="organizations">
                            <input type="hidden" name="term" value="${query}">
                            <button type="submit">
                                Barchasini ko'rish
                            </button>
                            </form>
                            </div>
                            <div class="mt-3.5 flex flex-col gap-1">`;
                            $.each(values, function (index, value) {
                                console.log(value)
                                html += `<a class="flex items-center gap-2.5 rounded-md border border-transparent p-1 hover:border-slate-100 hover:bg-slate-50/80" href="/tashkent/organizations/` + value.slug + `">
                                                <div class="truncate font-medium">` + value.name + `</div>
                                            </a>`;
                            })
                            html += `</div></div>`;
                        }


                    });
                    $('#search_result').html(html);
                }
            });
        } else {
            $('#search_result').hide();
        }
    });
</script>
