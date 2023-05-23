jQuery(document).ready(function($) {
    $(window).load(function(){
      $('.mos-vmb-wrapper .tab-con').hide();
      $('.mos-vmb-wrapper .tab-con.active').show();
    });

    $('.mos-vmb-wrapper .tab-nav > a').click(function(event) {
      event.preventDefault();
      var id = $(this).data('id');

      set_mos_vmb_cookie('plugin_active_tab',id,1);
      $('#mos-vmb-'+id).addClass('active').show();
      $('#mos-vmb-'+id).siblings('div').removeClass('active').hide();

      $(this).closest('.tab-nav').addClass('active');
      $(this).closest('.tab-nav').siblings().removeClass('active');
    });
});
