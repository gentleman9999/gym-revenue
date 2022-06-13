import { toastInfo } from "./createToast";

export const comingSoon = () => {
    return toastInfo("Feature Coming Soon!", {
        pauseOnHover: true,
    });
};
