export const comingSoon = () => {
    return new Noty({
        type: "warning",
        theme: "sunset",
        text: "Feature Coming Soon!",
        timeout: 7500,
    }).show();
};
