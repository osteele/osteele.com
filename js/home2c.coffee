$ ->
  $(".shorten").contractMores()
  $("a:not([title])").setTitlesFromMap kLinkTitleMap
  $("img:not([title])").setImageTitles()
  $(".candids img").mouseover(->
    $(this).stop().animate opacity: 1
  ).mouseout ->
    $(this).stop().animate opacity: .75

  0 and $("a:not(.no-link-icon)").live("mouseover", ->
    $(this).stop(true).css backgroundColor: "yellow"
  ).live("mouseout", ->
    $(this).stop(true).animate
      backgroundColor: "transparent"
    , "slow"
  )
  $("h1 img").mouseover (done) ->
    $this = $(this)
    $egg = $("h1 iframe")
    small = $this.bounds()
    small.width = small.height = Math.max(small.width, small.height)
    large =
      left: small.width + $("body").width() - 400
      top: 20
      width: 300
      height: 300

    cycle = $("h1 .caption").crossfader()
    $("h1 img").attr "title", ""
    if $egg.filter(":visible").length
      $(".hide-egg").removeClass "visible"
      large = $egg.bounds()
      $(".candids").show "slow"
      cycle.stop()
      $egg.css(
        position: "absolute"
        right: "inherit"
      ).css(large).animate small, ->
        $egg.hide()
        done()
    else
      $egg.show().css(small).animate large, ->
        $egg.css
          position: "fixed"
          left: "inherit"
          right: 50

        cycle.start()
        done()

      $(".candids").hide "slow"
  .nullifyWhileExecutingK()

(($) ->
  $.extend $.fn,
    bounds: ->
      return null  unless this[0]
      $.extend @offset(),
        width: @width()
        height: @height()

    contractMores: ->
      @each ->
        contract = ->
          $this.html html.replace(/<!--\s*more\s*-->(.|\s|\n)*/, "<span class=\"more\"></span>")
          $this.find(".more").click expand
        expand = ->
          $this.html html + "<span class=\"less\"></span>"
          $this.find(".less").click contract
        $this = $(this)
        html = $this.html()
        contract()

    crossfader: (options) ->
      options = options or {}
      period = options.period or 5000
      hangTime = options.hangTime or 2000
      transitionTime = period - hangTime
      period = (transitionTime + hangTime) * @length
      $es = this
      start: ->
        cycle = ($e) ->
          $e.animate(
            opacity: 1
          , transitionTime).animate(
            opacity: 1
          , hangTime).animate(
            opacity: 0
          , transitionTime).animate
            opacity: 0
          , ->
            cycle $e
        $es.stop(true).css(
          display: "block"
          opacity: 0
        ).each((i) ->
          $(this).animate
            opacity: 0
          , period * i / $es.length
        ).each ->
          cycle $(this)

      stop: ->
        $es.stop(true).animate
          opacity: 0
        , transitionTime / 2

    setTitlesFromMap: (map) ->
      @each ->
        $this = $(this)
        href = $this.attr("href")
        if href of map
          $this.attr "title", map[href].replace(/\.\.\./g, "…")
        else console.info "No title entry for ", href  if window.location.search.match(/\breport-missing-titles\b/) and window.console and console.info and $.isFunction(console.info)

    setImageTitles: ->
      @each ->
        $this = $(this)
        $this.attr "title", $this.attr("alt")

    toggling: (className, onadd, onremove) ->
      if @hasClass(className)
        @removeClass className
        $.isFunction(onremove) and onremove(this)
      else
        @addClass className
        $.isFunction(onadd) and onadd(this)
) jQuery
Function::nullifyWhileExecutingK = ->
  fn = this
  guard = false
  ->
    return  if guard
    guard = true
    fn.call this, ->
      guard = false

Function::serializedK = ->
  k = ->
    if pending.length
      ap = pending.shift()
      fn.apply ap[0], ap[1]
    else
      active = false
  fn = this
  pending = []
  active = false
  return ->
    args = Array::slice.call(arguments, 0)
    args.unshift k
    unless active
      active = true
      fn.apply this, args

$ ->
  $(".bottom-tab").each ->
    loadContent = ->
      $iframe.attr("src") or $iframe.attr("src", "/projects")
    $tab = $(this)
    $title = $tab.find(".tab-title")
    $content = $tab.find(".content")
    $iframe = $content.find("iframe")
    openCss = top: 5
    closedCss =
      position: $tab.css("position")
      top: $tab.css("top")
      bottom: $tab.css("bottom")
      zIndex: $tab.css("zIndex")

    closedHeight = undefined
    duration = 2000
    $title.mouseover(->
      $tab.hasClass("open") or $tab.stop(true).animate(bottom: -2)
      loadContent()
    ).mouseout(->
      $tab.hasClass("open") or $tab.stop(true).animate(bottom: closedCss.bottom)
    ).click (done) ->
      $tab.toggling "open", (->
        y = $tab.offset().top
        closedHeight = $tab.height()
        loadContent()
        $content.show()
        $tab.css(
          position: "fixed"
          top: y
          bottom: "inherit"
          zIndex: 100
        ).animate openCss, duration, done
      ), ->
        y = $(window).height() - closedHeight - parseInt(closedCss.bottom, 10)
        $tab.css closedCss
        $content.hide()
        done()
        return
        $tab.animate
          top: y
        , duration, ->
          $tab.css closedCss
          $content.hide()
          done()
    .nullifyWhileExecutingK()

$ ->
  name = $("title").text().match(/(.+?)(?=\s+HTML)/)[0]
  $("#person-controls .p").mouseover((k) ->
    setPersonClass = (className) ->
      $("body").removeClass("person-1 person-2 person-3").addClass className
      $("#person-controls div").removeClass "selected"
      $this.addClass "selected"
      $title.text $title.text().replace(/(.+?)(?=\s+HTML)/,
        1: "My"
        2: "Your"
        3: name
      [{p}])
    $this = $(this)
    $title = $("title")
    p = parseInt($this.text())
    className = "person-" + p
    return  if $("body").hasClass(className)
    $b = $("<div/>").css($.extend(
      position: "absolute"
      background: "blue"
      zIndex: 5
      opacity: .5
    , $this.bounds())).appendTo("body")
    $b.animate
      left: 0
      top: 0
      width: $(window).width() - 1
      height: $(window).height() - 1
      opacity: 0
    , ->
      $b.remove()
      setPersonClass className
      $(".ego").stop().css("backgroundColor", "#88f").animate
        backgroundColor: "white"
      , ->
        $(this).css "backgroundColor", "inherit"
  ).each ->
    $this = $(this)
    t = $this.text()
    $this.attr "title", "Change the page text to " + t + " person."

  $("p").personalize
    fullName: "Oliver Steele"
    gender: "m"

(($) ->
  person = (str, person, map) ->
    applyMap = (smap) ->
      re = map.expand(/\b((?:firstName(?:\s+lastName)?)|He|he)(?:\s+(is|was))?\b/g)
      str.replace(re, (_, s, v) ->
        smap.He + (if v of smap then " " + smap[v] else v or "")
      ).replace(/\bHis\b/, smap.His).replace /\bhis\b/, smap.his
    switch person
      when 1
        return applyMap(person1)
      when 2
        return applyMap(person2)
      when 3
        return str.replace(map.expand(/firstName(?:\s+lastName)?/g), "He")
  $.fn.personalize = (options) ->
    options = $.extend({}, options)
    if options.fullName
      names = options.fullName.match(/(.+?)\s+(.+)/)
      $.extend options,
        firstName: names[1]
        lastName: names[2]
    map = $.extend({}, options)
    gender_extensions =
      if options.gender.match(/^m/i)
        he: "he"
        his: "his"
       else
        he: "she"
        his: "her"
    $.extend map, gender_extensions
    $.extend map,
      He: map.he.capitalize()
      His: map.his.capitalize()
      expand: (s) ->
        return eval(@expand(s.toString()))  if s instanceof RegExp
        s.replace /\b(firstName|lastName|he|his|He|His)\b/g, (_, s) ->
          map[s]

    sel = map.expand("*:contains(firstName), *:contains(he), *:contains(his)")
    re = map.expand(/\b((firstName(\s+lastName)?|He|he)(\s+(is|was))?|His|his)\b/g)
    @filter(sel).each ->
      $this = $(this)
      $this.html $this.html().replace(re, (_, s) ->
        "<span class=\"ego\">" + "<span class=\"person-1\">" + person(s, 1, map) + "</span>" + "<span class=\"person-2\">" + person(s, 2, map) + "</span>" + "<span class=\"person-3\">" + s + " </span>" + "</span>"
      )

  person1 =
    He: "I"
    is: "am"
    was: "was"
    His: "My"
    his: "my"

  person2 =
    He: "You"
    is: "are"
    was: "were"
    His: "Your"
    his: "your"

  unless String::capitalize
    String::capitalize = ->
      @slice(0, 1).toUpperCase() + @slice(1)
) jQuery
