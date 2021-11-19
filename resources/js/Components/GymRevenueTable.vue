<template>
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow overflow-hidden border-b border-base-100 sm:rounded-lg">
                    <slot name="prethead" />
                    <table class="min-w-full divide-y divide-base-100">
                        <thead class="bg-base-100">
                        <tr>
                            <th v-for="(header, index) in headers" :key="index" scope="col" class="px-6 py-3 text-left text-xs font-medium  uppercase tracking-wider">
                                {{ header }}
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-base-300 divide-y divide-base-100">
                        <slot />
                        </tbody>
                    </table>

                    <slot name="pagination" />

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "GymRevenueTable",
    data() {
        return {
            items: 3,
        }
    },
    props: {
        headers: {
            type: Array,
            required: true
        },
    }
}
</script>

<style scoped>

</style>
