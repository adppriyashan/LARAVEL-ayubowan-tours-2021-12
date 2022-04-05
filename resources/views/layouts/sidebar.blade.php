    <div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
        <div class="main-menu-content">
            <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
                <li class=" nav-item"><a href="/"><i class="mbri-desktop"></i><span class="menu-title" data-i18n="Dashboard">Dashboard</span></a>

                </li>
                @if(doPermitted('//drivers') | doPermitted('//executives') | doPermitted('//representatives') | doPermitted('//vehicletypes') | doPermitted('//locations'))
                <li class=" nav-item"><a href="#"><i class="mbri-sites"></i><span class="menu-title" data-i18n="Templates">File</span></a>
                    <ul class="menu-content">
                        @if(doPermitted('//drivers'))
                        <li><a class="menu-item" href="/drivers"><i class="la la-user"></i><span data-i18n="Vertical">Drivers</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//executives'))
                        <li><a class="menu-item" href="/executives"><i class="la la-user"></i><span data-i18n="Horizontal">Executive</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//representatives'))
                        <li><a class="menu-item" href="/representatives"><i class="la la-user"></i><span data-i18n="Horizontal">Representative</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//vehicletypes'))
                        <li><a class="menu-item" href="/vehicletypes"><i class="la la-car"></i><span data-i18n="Horizontal">Vehicle Type</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//locations'))
                        <li><a class="menu-item" href="/locations"><i class="la la-map-marker"></i><span data-i18n="Horizontal">Location</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(doPermitted('//pricing'))
                <li class=" nav-item"><a href="/pricing"><i class="mbri-cash"></i><span class="menu-title" data-i18n="Admin Panels">Pricing</span></a>

                </li>
                @endif
                @if(doPermitted('//invoice'))
                <li class=" nav-item"><a href="/invoice"><i class="mbri-edit"></i><span class="menu-title" data-i18n="Apps">Invoice</span></a>

                </li>
                @endif
                @if(doPermitted('//invoice'))
                <li class=" nav-item"><a href="#"><i class="mbri-database"></i><span class="menu-title" data-i18n="Pages">Reports</span></a>
                    <ul class="menu-content">
                        {{-- <li><a class="menu-item" href="/drivercomissions"><i class="la la-user"></i><span>Driver Comissions</span></a>
                        </li>
                        <li><a class="menu-item" href="/repsales"><i class="la la-male"></i><span>Rep Sales</span></a>
                        </li> --}}
                        @if(doPermitted('//invoice'))
                        <li><a class="menu-item" href="/invoice/billed/list"><i class="la la-money"></i><span>Sales Report</span></a>
                        </li>
                        @endif

                    </ul>
                </li>
                @endif
                @if(doPermitted('//users') || doPermitted('//representatives/assign'))
                <li class=" nav-item"><a href="#"><i class="mbri-setting3"></i><span class="menu-title" data-i18n="Pages">System</span></a>
                    <ul class="menu-content">
                        @if(doPermitted('//users'))
                        <li><a class="menu-item" href="/users"><i class="la la-user-plus"></i><span>Users</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//representatives/assign'))
                        <li><a class="menu-item" href="/representatives/assign"><i class="la la-suitcase"></i><span>Assign Rep</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//users'))
                        <li><a class="menu-item" href="/usertypes"><i class="la la-key"></i><span>Permission Levels</span></a>
                        </li>
                        @endif
                        @if(doPermitted('//configuration'))
                        <li><a class="menu-item" href="/configuration"><i class="la la-gears"></i><span>Configuration</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif


            </ul>
        </div>
    </div>
