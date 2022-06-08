import { useToast } from "vue-toastification";

export const comingSoon = () => {
    const toast = useToast();
    return toast.info("Feature Coming Soon!", {
        timeout: 7500,
        pauseOnHover: false,
        closeOnClick: true,
    });
};
