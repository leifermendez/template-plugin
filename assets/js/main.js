// /**
//  * Archivo principal de JS
//  **/
//
// (function ($) {
//     let endpoint = ``
//     $(document).ready(LoadInput);
//     let siteUrl, endPoint;
//     siteUrl = backend_data.siteUrl;
//     endPoint = `${siteUrl}/wp-admin/admin-ajax.php`;
//
//     console.log(endPoint)
//
// }(jQuery));
//
//
// /**
//  **/
//
// function LoadInput() {
//     // const cities = new Bloodhound({
//     //     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
//     //     queryTokenizer: Bloodhound.tokenizers.whitespace,
//     //     prefetch: 'http://localhost/wordpress/wp-content/plugins/comments_plugin/dependencies/bootstrap-tagsinput-latest/examples/assets/cities.json'
//     // });
//     //
//     //
//     // cities.initialize();
//
//     const tagObj = $('#input-tags');
//     console.log('--',tagObj)
//     tagObj.tagsinput({
//         itemValue: function(item) {
//             return item.ID;
//         },
//         typeahead: {
//             source: function(query) {
//                 console.log(query)
//                 return $.get('http://localhost/wordpress/wp-admin/admin-ajax.php?action=get_source_tag&src=Pro&sources=products');
//             }
//         }
//     });
// // elt.tagsinput('add', { "value": 1 , "text": "Amsterdam"   , "continent": "Europe"    });
// }