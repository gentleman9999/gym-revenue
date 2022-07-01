<template>
    <dashboard-widget
        title="Communications"
        :modal-component="CommunicationsModal"
        :amount="18000"
    >
        <div class="flex flex-col gap-2 mr-16">
            <div class="flex flex-row gap-2 items-center" v-for="lead in leads">
                <span class="text-secondary text-lg flex-grow">{{ lead }}</span>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="18.745"
                    height="18.746"
                    viewBox="0 0 15.745 15.746"
                >
                    <g
                        id="Group_1318"
                        data-name="Group 1318"
                        transform="translate(-283.061 -1062.869)"
                    >
                        <path
                            id="Path_2712"
                            data-name="Path 2712"
                            d="M1.361,4.152V14.213H14.307V4.152ZM12.793,1.968h1.968a.93.93,0,0,1,.984.984v11.81a.93.93,0,0,1-.984.984H.984A.93.93,0,0,1,0,14.762V2.952a.93.93,0,0,1,.984-.984H2.952V.984A.93.93,0,0,1,3.936,0,.93.93,0,0,1,4.92.984v.984h5.9V.984a.984.984,0,1,1,1.968,0Z"
                            transform="translate(283.061 1062.869)"
                            fill="#a8a8a8"
                            fill-rule="evenodd"
                            opacity="0.4"
                        />
                        <path
                            id="Path_2713"
                            data-name="Path 2713"
                            d="M15.906,16.454H13.376V13.924h2.529Zm-3.794,0H9.583V13.924h2.529Zm3.794-3.794H13.376V10.13h2.529Zm-3.794,0H9.583V10.13h2.529ZM8.318,16.454H5.789V13.924H8.318Z"
                            transform="translate(280.086 1058.925)"
                            fill="#a8a8a8"
                            fill-rule="evenodd"
                        />
                        <path
                            id="Path_2714"
                            data-name="Path 2714"
                            d="M13.9,12.66H11.368V10.13H13.9Z"
                            transform="translate(274.506 1058.925)"
                            fill="#a8a8a8"
                            fill-rule="evenodd"
                        />
                    </g>
                </svg>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="18.967"
                    height="18.201"
                    viewBox="0 0 17.967 16.201"
                >
                    <g
                        id="Group_1319"
                        data-name="Group 1319"
                        transform="translate(-327.645 -1062.869)"
                    >
                        <path
                            id="comm"
                            d="M16.321,22.884h3.122a.326.326,0,0,1,.23.1l2.358,2.357V23.21a.326.326,0,0,1,.326-.326h.924a1.335,1.335,0,0,0,1.333-1.333V17.472a1.335,1.335,0,0,0-1.333-1.333H21.213v3.383a1.988,1.988,0,0,1-1.985,1.985H16.241l-.919.919a1.339,1.339,0,0,0,1,.458Z"
                            transform="translate(320.996 1053.732)"
                            fill="#fff"
                        />
                        <path
                            id="comm-2"
                            data-name="comm"
                            d="M5.463,17.278a1.406,1.406,0,0,0,1.4,1.4h.973a.343.343,0,0,1,.343.343v2.241L9.4,20.047l1.264-1.264a.343.343,0,0,1,.243-.1H14.2a1.406,1.406,0,0,0,1.4-1.4v-4.3a1.406,1.406,0,0,0-1.4-1.4H6.869a1.406,1.406,0,0,0-1.4,1.4Z"
                            transform="translate(325.785 1055.814)"
                            fill="#fff"
                            opacity="0.423"
                        />
                        <g
                            id="Group_405"
                            data-name="Group 405"
                            transform="translate(327.645 1062.869)"
                        >
                            <ellipse
                                id="Ellipse_37"
                                data-name="Ellipse 37"
                                cx="3.995"
                                cy="3.995"
                                rx="3.995"
                                ry="3.995"
                                transform="translate(0 0)"
                                fill="red"
                            />
                            <text
                                id="_2"
                                data-name="2"
                                transform="translate(2.492 5.601)"
                                fill="#fff"
                                font-size="5"
                                font-family="Muli-SemiBold, Muli"
                                font-weight="600"
                            >
                                <tspan x="0" y="0">2</tspan>
                            </text>
                        </g>
                    </g>
                </svg>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="18.741"
                    height="18.773"
                    viewBox="0 0 15.741 13.773"
                >
                    <g
                        id="Group_1320"
                        data-name="Group 1320"
                        transform="translate(-379.379 -1063.727)"
                    >
                        <path
                            id="Path_358"
                            data-name="Path 358"
                            d="M14.757,1H.984A.984.984,0,0,0,0,1.984V13.79a.984.984,0,0,0,.984.984H14.757a.984.984,0,0,0,.984-.984V1.984A.984.984,0,0,0,14.757,1Zm-.984,11.806H1.968V6.63L7.383,9.724a.982.982,0,0,0,.976,0L13.773,6.63Zm0-8.441-5.9,3.374-5.9-3.374v-1.4H13.773Z"
                            transform="translate(379.379 1062.727)"
                            fill="#fff"
                        />
                    </g>
                </svg>
            </div>
        </div>
    </dashboard-widget>
</template>

<script>
import { defineComponent } from "vue";
import DashboardWidget from "@/Pages/Reports/Partials/DashboardWidget.vue";
import CommunicationsModal from "@/Pages/Reports/Communications/CommunicationsModal.vue";

export default defineComponent({
    components: {
        DashboardWidget,
    },
    setup() {
        const leads = [
            "Steve B",
            "Kelly A",
            "Nancy E",
            "David G",
            "Paul S",
            "Ed K",
            "David T",
        ];

        return { CommunicationsModal, leads };
    },
});
</script>
