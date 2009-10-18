$(function() {
  $('.shorten').each(function() {
    var $this = $(this);
    var html = $this.html();
    shrink();
    function shrink() {
      $this.html(html.replace(/<!-- more -->(.|\s|\n)*/, '<span class="more"></span>'));
      $this.find('.more').click(grow);
    }
    function grow() {
      $this.html(html + '<span class="less"></span>');
      $this.find('.less').click(shrink);
    }
  });
});