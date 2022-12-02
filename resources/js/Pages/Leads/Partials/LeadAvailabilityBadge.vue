<template>
    <div
        class="badge"
        :class="{ 'cursor-pointer': status === 'Available', ...badgeClass }"
        @click.prevent.stop="handleClick"
    >
        {{ assigning ? "Assigning..." : status }}
    </div>
</template>

<script>
import { computed, ref } from "vue";
import { usePage } from "@inertiajs/inertia-vue3";
import { useMutation } from "@vue/apollo-composable";
import gql from "graphql-tag";

export default {
    props: {
        value: {
            type: Object,
            required: true,
        },
        data: {
            type: Object,
            required: true,
        },
        fields: {
            type: Array,
        },
        modelName: {
            type: String,
            default: "record",
        },
        modelNamePlural: {
            type: String,
        },
        titleField: {
            type: String,
        },
        actions: {
            type: Object,
            default: {},
        },
    },
    setup(props) {
        const page = usePage();
        const claimed = ref(props?.value);
        const assigning = ref(false);

        const { mutate: claimEndUser } = useMutation(gql`
            mutation claimEndUser($endUser: ID!) {
                claimEndUser(endUser: $endUser) {
                    owner_user_id
                }
            }
        `);

        const status = computed(() => {
            if (assigning.value === true) {
                return "Assigning...";
            }

            const yours = claimed.value === page.props.value.user.id;
            if (yours) {
                return "Yours";
            } else if (claimed.value) {
                return "Claimed";
            }
            return "Available";
        });

        const badgeClass = computed(() => {
            switch (status.value) {
                case "Yours":
                    return { "badge-info": true };
                case "Assigning...":
                    return { "badge-warning": true };
                case "Available":
                    return { "badge-success": true };
                case "Claimed":
                    return { "badge-error": true };
            }
        });

        const handleClick = async () => {
            console.log("handleClick");
            if (status.value === "Available") {
                assigning.value = true;
                const { data } = await claimEndUser({ endUser: props.data.id });
                claimed.value = data.claimEndUser.owner_user_id;
                assigning.value = false;
            }
        };
        return { status, handleClick, badgeClass, assigning };
    },
};
</script>
