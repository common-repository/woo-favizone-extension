function favizoneRecommender(k){
    setTimeout(function() {
        var script = document.createElement('script');
        script.setAttribute('type', 'text/javascript');
        //script.setAttribute('src', 'http://b41ba870.ngrok.io/favizone/api/custom-scripts/'+k);
        script.setAttribute('src', 'https://api.favizone.com/api/custom-scripts/'+k);
        document.getElementsByTagName('head')[0].appendChild(script);
    }, 100);
}
favizoneRecommender(php_vars.favizone_shop_id);
