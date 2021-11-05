<template>
    <div class="flex items-center">
        <div class="relative mr-4 lg:mx-0 w-full">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-500" viewBox="0 0 24 24" fill="none">
                        <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </span>
            <input v-model="searchInput" class="form-input w-full sm:w-56 rounded-md pl-10 pr-4 p-2 bg-gray-100 border-1 border-transparent focus:bg-transparent focus:border-indigo-300" type="text" placeholder="Search" @change="validateInput()">
        </div>
    </div>
</template>

<script>
export default {
    name: "JetBarSidebarSearch",
    data() {
        return {
            searchInput: ''
        }
    },
    methods: {
        validateInput() {
            console.log(`Derp ${this.searchInput}`);
            this.comingSoon();
        },
        comingSoon() {
            new Noty({
                type: 'warning',
                theme: 'sunset',
                text: 'Feature Coming Soon!',
                timeout: 7500
            }).show();
        },
    },
}
</script>

<style scoped>

</style>
