<!--@TODO: this is the worst icon component implementation I've came across. Let's switch this out for FA, Feather, hell, anything really-->
<template>
    <svg
        v-if="type === 'users' && fill"
        class="w-5 h-5"
        fill="currentColor"
        viewBox="0 0 20 20"
    >
        <path
            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
        ></path>
    </svg>

    <svg
        v-if="type === 'users' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'money' && fill"
        class="w-5 h-5"
        fill="currentColor"
        viewBox="0 0 20 20"
    >
        <path
            fill-rule="evenodd"
            d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'money' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'cart' && fill"
        class="w-5 h-5"
        fill="currentColor"
        viewBox="0 0 20 20"
    >
        <path
            d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
        ></path>
    </svg>

    <svg
        v-if="type === 'cart' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'message' && fill"
        class="w-5 h-5"
        fill="currentColor"
        viewBox="0 0 20 20"
    >
        <path
            fill-rule="evenodd"
            d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'message' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
        ></path>
    </svg>

    <svg
        v-if="type === 'eye' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
        <path
            fill-rule="evenodd"
            d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'eye' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
        ></path>
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
        ></path>
    </svg>

    <svg
        v-if="type === 'trash' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'trash' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
        ></path>
    </svg>

    <svg
        v-if="type === 'search' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'search' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'x' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'x' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
        ></path>
    </svg>

    <svg
        v-if="type === 'plus' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'plus' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 6v6m0 0v6m0-6h6m-6 0H6"
        ></path>
    </svg>

    <svg
        v-if="type === 'pencil' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
        ></path>
    </svg>

    <svg
        v-if="type === 'pencil' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
        ></path>
    </svg>

    <svg
        v-if="type === 'pencil-alt' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z"
        ></path>
        <path
            fill-rule="evenodd"
            d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'pencil-alt' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
        ></path>
    </svg>

    <svg
        v-if="type === 'information-circle' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'information-circle' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'check-circle' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'check-circle' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'x-circle' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'x-circle' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'exclamation-circle' && fill"
        class="w-6 h-6"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            fill-rule="evenodd"
            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
            clip-rule="evenodd"
        ></path>
    </svg>

    <svg
        v-if="type === 'exclamation-circle' && !fill"
        class="w-6 h-6"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        xmlns="http://www.w3.org/2000/svg"
    >
        <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        ></path>
    </svg>

    <svg
        v-if="type === 'untrash' && fill"
        xmlns="http://www.w3.org/2000/svg"
        class="w-6 h-6"
        fill="currentColor"
        role="img"
        focusable="false"
        aria-hidden="true"
        viewBox="0 0 14 14"
    >
        <path
            d="m 3.9587397,12.277725 c -0.42211,-0.1956 -0.65803,-0.532 -0.72456,-1.033 -0.0732,-0.5514 -0.44436,-3.9027999 -0.43353,-3.9147999 0.006,-0.01 0.0844,0.01 0.17447,0.032 0.18456,0.05 0.42832,0.023 0.55694,-0.061 0.068,-0.044 0.0894,-0.046 0.1017,-0.01 0.009,0.027 0.11156,0.9206 0.22891,1.9862 0.11735,1.0655999 0.22208,1.9664999 0.23272,2.0021999 0.0106,0.036 0.0711,0.1083 0.13421,0.1614 l 0.11485,0.097 2.1986,0 c 1.62368,0 2.22464,-0.012 2.2982,-0.045 0.0548,-0.025 0.12307,-0.091 0.15175,-0.1462 0.0315,-0.061 0.15952,-1.0558 0.32296,-2.5092999 0.14894,-1.3247 0.30879,-2.7445 0.35523,-3.1551 l 0.0844,-0.7467 -1.36942,-0.5803 c -0.75318,-0.3192 -1.90602,-0.8075 -2.56186,-1.0852 -0.65584,-0.2777 -1.22303,-0.5326 -1.26041,-0.5664 -0.19254,-0.1743 -0.24225,-0.5353 -0.10686,-0.7762 0.16289,-0.2898 0.53087,-0.3822 0.89459,-0.2245 0.10097,0.044 0.58531,0.2503 1.07631,0.4589 0.49099,0.2086 0.91164,0.3873 0.93477,0.3972 0.0231,0.01 0.0874,-0.075 0.14292,-0.1882 0.13455,-0.275 0.40789,-0.525 0.69067,-0.6317 0.35908,-0.1355 0.64306,-0.1006 1.23269,0.1514 0.5658,0.2418 0.7028093,0.3227 0.8713093,0.5146 0.28506,0.3247 0.37291,0.7746 0.23225,1.1895 -0.0453,0.1335 -0.0685,0.2554 -0.0516,0.2708 0.0169,0.015 0.52645,0.2371 1.13236,0.4927 0.60591,0.2557 1.14232,0.5013 1.19202,0.546 0.20589,0.1849 0.25867,0.5645 0.10907,0.7843 -0.14599,0.2145 -0.27358,0.2847 -0.51562,0.2835 -0.18612,-9e-4 -0.32618,-0.048 -1.02289,-0.347 -0.44399,-0.1903 -0.80725,-0.3425 -0.80725,-0.3382 0,0 -0.0941,0.8443 -0.20922,1.8666 -0.11507,1.0223 -0.26959,2.3972 -0.34339,3.0552999 -0.073799,0.6581 -0.1596193,1.2735 -0.1907193,1.3676 -0.0704,0.2128 -0.23244,0.4301 -0.42802,0.574 -0.31069,0.2284 -0.25076,0.2237 -2.85065,0.2233 l -2.37896,-3e-4 -0.20894,-0.097 z m 5.78177,-9.0863999 c 0,-0.1942 -0.14548,-0.3112 -0.64273,-0.5167 -0.47163,-0.1949 -0.49985,-0.2002 -0.65322,-0.1208 -0.11486,0.059 -0.25934,0.313 -0.20633,0.3622 0.0271,0.025 1.06768,0.4737 1.3194,0.5687 0.0637,0.024 0.0909,0.011 0.13159,-0.065 0.0282,-0.053 0.0513,-0.1552 0.0513,-0.2284 z m -4.78159,7.5330999 c -0.0663,-0.029 -0.14167,-0.092 -0.16757,-0.1404 -0.0458,-0.085 -0.14383,-0.8205999 -0.14717,-1.1033999 l -0.002,-0.1406 0.16145,0.073 c 0.19084,0.087 0.29842,0.09 0.50189,0.018 0.0828,-0.029 0.15533,-0.049 0.16123,-0.043 0.0249,0.025 0.11652,0.9907999 0.0992,1.0459999 -0.0965,0.3079 -0.32042,0.4149 -0.60738,0.29 z m 1.36953,-0.017 c -0.1763,-0.1075 -0.2044,-0.2415 -0.2044,-0.9745999 l 0,-0.6558 0.19944,-0.1226 c 0.10969,-0.067 0.29643,-0.197 0.41498,-0.2881 0.11855,-0.091 0.22539,-0.1657 0.23743,-0.1657 0.012,0 0.0219,0.4317 0.0218,0.9592 -4e-5,0.8372999 -0.008,0.9760999 -0.0665,1.0916999 -0.11371,0.2267 -0.37558,0.2944 -0.60279,0.1559 z m 1.44134,0.011 c -0.0765,-0.038 -0.14062,-0.1104 -0.17518,-0.1986 -0.0498,-0.1271 -0.0353,-0.3209 0.16771,-2.2471999 0.2451,-2.3255 0.22896,-2.2513 0.51149,-2.3507 0.20101,-0.071 0.40555,0.01 0.49388,0.1954 0.058,0.1217 0.05,0.2364 -0.15174,2.1769 -0.11713,1.1267 -0.23091,2.1134999 -0.25285,2.1927999 -0.0696,0.2517 -0.33904,0.3568 -0.59331,0.2314 z m -2.79487,-1.8402999 c -0.0366,-0.022 -0.0665,-0.079 -0.0665,-0.1277 0,-0.069 0.0561,-0.1207 0.254,-0.2356 0.13969,-0.081 0.35224,-0.238 0.47232,-0.3486 0.98933,-0.9117 0.13301,-2.0157 -1.78998,-2.3078 -0.19849,-0.03 -0.37371,-0.055 -0.38938,-0.055 -0.0157,-10e-5 -0.0285,0.1964 -0.0285,0.4367 0,0.3862 -0.009,0.4456 -0.076,0.5128 -0.0931,0.093 -0.21838,0.097 -0.34293,0.011 -0.24702,-0.1707 -1.95973,-1.7043 -1.99116,-1.7829 -0.0224,-0.056 -0.0224,-0.1244 0,-0.1828 0.0197,-0.051 0.48708,-0.4872 1.03861,-0.9687 1.06851,-0.9328 1.1264,-0.9685 1.29548,-0.7994 0.0667,0.067 0.076,0.1266 0.076,0.4899 l 0,0.4139 0.33201,0.047 c 0.80296,0.1139 1.66768,0.4 2.26534,0.7494 0.42462,0.2482 0.95481,0.7765 1.11706,1.1131 0.24465,0.5076 0.24994,0.9941 0.0162,1.4931 -0.2442,0.5215 -0.718,0.9455 -1.47037,1.316 -0.50585,0.249 -0.61481,0.2835 -0.71227,0.2254 z"
        />
    </svg>
</template>

<script>
export default {
    name: "JetBarIcon",
    props: { type: String, fill: Boolean },
};
</script>
