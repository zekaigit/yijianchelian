/**
 * @fileoverview �ٶȵ�ͼ�Ĺ켣�����࣬���⿪�š�
 * �û������ڵ�ͼ���Զ���켣�˶�
 * �����Զ���·��ĳ�����ͼƬ�����ֽ��ܵȡ�
 * ���������<a href="symbols/BMapLib.LuShu.html">LuShu</a>��
 * ����Baidu Map API 1.2��.
 *
 * @author Baidu Map Api Group
 * @version 1.2
 */

/**
 * @namespace BMap������library�������BMapLib�����ռ���
 */
var BMapLib = window.BMapLib = BMapLib || {};

(function() {
    //����baidu��
    var T, baidu = T = baidu || {version: '1.5.0'};
    baidu.guid = '$BAIDU$';
    //���·���Ϊ�ٶ�Tangram����еķ������뵽http://tangram.baidu.com �鿴�ĵ�
    (function() {
        window[baidu.guid] = window[baidu.guid] || {};
        baidu.dom = baidu.dom || {};
        baidu.dom.g = function(id) {
            if ('string' == typeof id || id instanceof String) {
                return document.getElementById(id);
            } else if (id && id.nodeName && (id.nodeType == 1 || id.nodeType == 9)) {
                return id;
            }
            return null;
        };
        baidu.g = baidu.G = baidu.dom.g;
        baidu.lang = baidu.lang || {};
        baidu.lang.isString = function(source) {
            return '[object String]' == Object.prototype.toString.call(source);
        };
        baidu.isString = baidu.lang.isString;
        baidu.dom._g = function(id) {
            if (baidu.lang.isString(id)) {
                return document.getElementById(id);
            }
            return id;
        };
        baidu._g = baidu.dom._g;
        baidu.dom.getDocument = function(element) {
            element = baidu.dom.g(element);
            return element.nodeType == 9 ? element : element.ownerDocument || element.document;
        };
        baidu.browser = baidu.browser || {};
        baidu.browser.ie = baidu.ie = /msie (\d+\.\d+)/i.test(navigator.userAgent) ? (document.documentMode || + RegExp['\x241']) : undefined;
        baidu.dom.getComputedStyle = function(element, key) {
            element = baidu.dom._g(element);
            var doc = baidu.dom.getDocument(element),
                styles;
            if (doc.defaultView && doc.defaultView.getComputedStyle) {
                styles = doc.defaultView.getComputedStyle(element, null);
                if (styles) {
                    return styles[key] || styles.getPropertyValue(key);
                }
            }
            return '';
        };
        baidu.dom._styleFixer = baidu.dom._styleFixer || {};
        baidu.dom._styleFilter = baidu.dom._styleFilter || [];
        baidu.dom._styleFilter.filter = function(key, value, method) {
            for (var i = 0, filters = baidu.dom._styleFilter, filter; filter = filters[i]; i++) {
                if (filter = filter[method]) {
                    value = filter(key, value);
                }
            }
            return value;
        };
        baidu.string = baidu.string || {};


        baidu.string.toCamelCase = function(source) {

            if (source.indexOf('-') < 0 && source.indexOf('_') < 0) {
                return source;
            }
            return source.replace(/[-_][^-_]/g, function(match) {
                return match.charAt(1).toUpperCase();
            });
        };
        baidu.dom.getStyle = function(element, key) {
            var dom = baidu.dom;
            element = dom.g(element);
            key = baidu.string.toCamelCase(key);

            var value = element.style[key] ||
                        (element.currentStyle ? element.currentStyle[key] : '') ||
                        dom.getComputedStyle(element, key);

            if (!value) {
                var fixer = dom._styleFixer[key];
                if (fixer) {
                    value = fixer.get ? fixer.get(element) : baidu.dom.getStyle(element, fixer);
                }
            }

            if (fixer = dom._styleFilter) {
                value = fixer.filter(key, value, 'get');
            }
            return value;
        };
        baidu.getStyle = baidu.dom.getStyle;
        baidu.dom._NAME_ATTRS = (function() {
            var result = {
                'cellpadding': 'cellPadding',
                'cellspacing': 'cellSpacing',
                'colspan': 'colSpan',
                'rowspan': 'rowSpan',
                'valign': 'vAlign',
                'usemap': 'useMap',
                'frameborder': 'frameBorder'
            };

            if (baidu.browser.ie < 8) {
                result['for'] = 'htmlFor';
                result['class'] = 'className';
            } else {
                result['htmlFor'] = 'for';
                result['className'] = 'class';
            }

            return result;
        })();
        baidu.dom.setAttr = function(element, key, value) {
            element = baidu.dom.g(element);
            if ('style' == key) {
                element.style.cssText = value;
            } else {
                key = baidu.dom._NAME_ATTRS[key] || key;
                element.setAttribute(key, value);
            }
            return element;
        };
        baidu.setAttr = baidu.dom.setAttr;
        baidu.dom.setAttrs = function(element, attributes) {
            element = baidu.dom.g(element);
            for (var key in attributes) {
                baidu.dom.setAttr(element, key, attributes[key]);
            }
            return element;
        };
        baidu.setAttrs = baidu.dom.setAttrs;
        baidu.dom.create = function(tagName, opt_attributes) {
            var el = document.createElement(tagName),
                attributes = opt_attributes || {};
            return baidu.dom.setAttrs(el, attributes);
        };
        baidu.object = baidu.object || {};
        baidu.extend =
        baidu.object.extend = function(target, source) {
            for (var p in source) {
                if (source.hasOwnProperty(p)) {
                    target[p] = source[p];
                }
            }
            return target;
        };
    })();

    /**
     * @exports LuShu as BMapLib.LuShu
     */
    var LuShu =
    /**
     * LuShu��Ĺ��캯��
     * @class LuShu <b>���</b>��
     * ʵ��������󣬿ɵ���,start,end,pause�ȷ������Ƹ�������˶���

     * @constructor
         * @param {Map} map Baidu map��ʵ������.
         * @param {Array} path ����·�ߵ�point������.
         * @param {Json Object} opts ��ѡ������������Ǳ����������ѡ�������<br />
         * {<br />"<b>landmarkPois</b>" : {Array} Ҫ�ڸ������ƶ������У���ʾ������㡣��ʽ����:landmarkPois:[<br />
         *      {lng:116.314782,lat:39.913508,html:'����վ',pauseTime:2},<br />
         *      {lng:116.315391,lat:39.964429,html:'���ٹ�·�շ�վ,pauseTime:3}]<br />
         * <br />"<b>icon</b>" : {Icon} �������icon,
         * <br />"<b>speed</b>" : {Number} �������ƶ��ٶȣ���λ��/��    <br />
         * <br />"<b>defaultContent</b>" : {String} �������е�����    <br />
         * }<br />.
         * @example <b>�ο�ʾ����</b><br />
         * var lushu = new BMapLib.LuShu(map,arrPois,{defaultContent:"�ӱ��������",landmarkPois:[]});
     */
     BMapLib.LuShu = function(map, path, opts) {
        if (!path || path.length < 1) {
            return;
        }
        this._map = map;
        //�洢һ��·��
        this._path = path;
        //�ƶ�����ǰ�������
        this.i = 0;
        //������ͣ��ʼ�ƶ��Ķ��е�����
        this._setTimeoutQuene = [];
        //��������ת������
        this._projection = this._map.getMapType().getProjection();
        this._opts = {
            icon: null,
            //Ĭ���ٶ� ��/��
            speed: 4000,
            defaultContent: ''
        };
        this._setOptions(opts);
        this._rotation = 0;//С��ת���ĽǶ�

        //�������Ĭ��ʵ������ʹ��Ĭ�ϵ�icon
        if (!this._opts.icon instanceof BMap.Icon) {
            this._opts.icon = defaultIcon;
        }
    }
     /**
     * �����û������opts���޸�Ĭ�ϲ���_opts
     * @param {Json Object} opts �û�������޸Ĳ���.
     * @return �޷���ֵ.
     */
    LuShu.prototype._setOptions = function(opts) {
        if (!opts) {
            return;
        }
        for (var p in opts) {
            if (opts.hasOwnProperty(p)) {
                this._opts[p] = opts[p];
            }
        }
    }

    /**
     * @description ��ʼ�˶�
     * @param none
     * @return �޷���ֵ.
     *
     * @example <b>�ο�ʾ����</b><br />
     * lushu.start();
     */
    LuShu.prototype.start = function() {
        var me = this,
            len = me._path.length;
        //���ǵ�һ�ε����ʼ,����С����û�����յ�
        if (me.i && me.i < len - 1) {
            //û��pause�ٰ�start��������
            if (!me._fromPause) {
                return;
            }else if(!me._fromStop){
	            //����pause��ť,�����ٰ�start��ֱ���ƶ�����һ��
	            //���Ҵ˹����У�û�а�stop��ť
	            //��ֹ��stop����pause��Ȼ��������ͣ��start���쳣
	            me._moveNext(++me.i);
            }
        }else {
            //��һ�ε����ʼ�����ߵ���stop֮��㿪ʼ
            me._addMarker();
            //�ȴ�marker��������ټ���infowindow
            me._timeoutFlag = setTimeout(function() {
                    me._addInfoWin();
                    if(me._opts.defaultContent == ""){
                        me.hideInfoWindow();
                    }
                    me._moveNext(me.i);
            },400);
        }
         //����״̬
        this._fromPause = false;
        this._fromStop = false;
    },
    /**
     * �����˶�
     * @return �޷���ֵ.
     *
     * @example <b>�ο�ʾ����</b><br />
     * lushu.stop();
     */
    LuShu.prototype.stop = function() {
        this.i = 0;
        this._fromStop = true;
        clearInterval(this._intervalFlag);
        this._clearTimeout();
        //����landmark��ߵ�poiΪδ��ʾ״̬
        for (var i = 0, t = this._opts.landmarkPois, len = t.length; i < len; i++) {
            t[i].bShow = false;
        }
    };
    /**
     * ��ͣ�˶�
     * @return �޷���ֵ.
     */
    LuShu.prototype.pause = function() {
        clearInterval(this._intervalFlag);

        //��ʶ�Ƿ��ǰ���pause��ť
        this._fromPause = true;
        this._clearTimeout();
    };
    /**
     * �����Ϸ�overlay
     * @return �޷���ֵ.
     *
     * @example <b>�ο�ʾ����</b><br />
     * lushu.hideInfoWindow();
     */
    LuShu.prototype.hideInfoWindow = function() {
        this._overlay._div.style.visibility = 'hidden';
    };
    /**
     * ��ʾ�Ϸ�overlay
     * @return �޷���ֵ.
     *
     * @example <b>�ο�ʾ����</b><br />
     * lushu.showInfoWindow();
     */
    LuShu.prototype.showInfoWindow = function() {
        this._overlay._div.style.visibility = 'visible';
    };
    //Lushu˽�з���
    baidu.object.extend(LuShu.prototype, {
        /**
         * ���marker����ͼ��
         * @param {Function} �ص�����.
         * @return �޷���ֵ.
         */
        _addMarker: function(callback) {
            if (this._marker) {
                this.stop();
                this._map.removeOverlay(this._marker);
                clearTimeout(this._timeoutFlag);
            }
            //�Ƴ�֮ǰ��overlay
            this._overlay && this._map.removeOverlay(this._overlay);
            var marker = new BMap.Marker(this._path[0]);
            this._opts.icon && marker.setIcon(this._opts.icon);
            this._map.addOverlay(marker);
            marker.setAnimation(BMAP_ANIMATION_DROP);
            this._marker = marker;
        },
        /**
         * ����Ϸ�overlay
         * @return �޷���ֵ.
         */
        _addInfoWin: function() {
            var me = this;
            //if(me._opts.defaultContent!== ""){
                var overlay = new CustomOverlay(me._marker.getPosition(), me._opts.defaultContent);

                //����ǰ������ô���overlay��
                overlay.setRelatedClass(this);
                this._overlay = overlay;
                this._map.addOverlay(overlay);

            //}

        },
        /**
         * ��ȡī��������
         * @param {Point} poi ��γ������.
         * @return �޷���ֵ.
         */
        _getMercator: function(poi) {
            return this._map.getMapType().getProjection().lngLatToPoint(poi);
        },
        /**
         * ���������ľ���
         * @param {Point} poi ��γ������A��.
         * @param {Point} poi ��γ������B��.
         * @return �޷���ֵ.
         */
        _getDistance: function(pxA, pxB) {
            return Math.sqrt(Math.pow(pxA.x - pxB.x, 2) + Math.pow(pxA.y - pxB.y, 2));
        },
          //Ŀ����  ��ǰ�Ĳ���,position,�ܵĲ���,����Ч��,�ص�
        /**
         * �ƶ�С��
         * @param {Number} poi ��ǰ�Ĳ���.
         * @param {Point} initPos ��γ�������ʼ��.
         * @param {Point} targetPos ��γ������Ŀ���.
         * @param {Function} effect ����Ч��.
         * @return �޷���ֵ.
         */
        _move: function(initPos,targetPos,effect) {
            var me = this,
                //��ǰ��֡��
                currentCount = 0,
                //��������/��
                timer = 10,
                step = this._opts.speed / (1000 / timer),
                //��ʼ����
                init_pos = this._projection.lngLatToPoint(initPos),
                //��ȡ�������(x,y)����
                target_pos = this._projection.lngLatToPoint(targetPos),
                //�ܵĲ���
                count = Math.round(me._getDistance(init_pos, target_pos) / step);

            //���С��1ֱ���ƶ�����һ��
            if (count < 1) {
                me._moveNext(++me.i);
                return;
            }
            //����֮�������ƶ�
            me._intervalFlag = setInterval(function() {
            //����֮�䵱ǰ֡��������֡����ʱ����˵���Ѿ�����ƶ�
	            if (currentCount >= count) {
	                clearInterval(me._intervalFlag);
	                //�ƶ��ĵ��Ѿ������ܵĳ���
		        	if(me.i > me._path.length){
						return;
		        	}
		        	//������һ����
	                me._moveNext(++me.i);
	            }else {
                        currentCount++;
                        var x = effect(init_pos.x, target_pos.x, currentCount, count),
                            y = effect(init_pos.y, target_pos.y, currentCount, count),
                            pos = me._projection.pointToLngLat(new BMap.Pixel(x, y));
                        //����marker
                        if(currentCount == 1){
                            var proPos = null;
                            if(me.i - 1 >= 0){
                                proPos = me._path[me.i - 1];
                            }
                            if(me._opts.enableRotation == true){
                                 me.setRotation(proPos,initPos,targetPos);
                            }
                            if(me._opts.autoView){
                                if(!me._map.getBounds().containsPoint(pos)){
                                    me._map.setCenter(pos);
                                }   
                            }
                        }
                        //�����ƶ�

                        me._marker.setPosition(pos);
                        //�����Զ���overlay��λ��
                        me._setInfoWin(pos);

                        

                        
	                }
	        },timer);
        },
        /**
        *��ÿ�������ʵ����������С��ת���ĽǶ�
        */
        setRotation : function(prePos,curPos,targetPos){
            var me = this;
            var deg = 0;
            //start!
            curPos =  me._map.pointToPixel(curPos);
            targetPos =  me._map.pointToPixel(targetPos);   

            if(targetPos.x != curPos.x){
                    var tan = (targetPos.y - curPos.y)/(targetPos.x - curPos.x),
                    atan  = Math.atan(tan);
                    deg = atan*360/(2*Math.PI);
                    //degree  correction;
                    if(targetPos.x < curPos.x){
                        deg = -deg + 90 + 90;

                    } else {
                        deg = -deg;
                    }

                    me._marker.setRotation(-deg);   

            }else {
                    var disy = targetPos.y- curPos.y ;
                    var bias = 0;
                    if(disy > 0)
                        bias=-1
                    else
                        bias = 1
                    me._marker.setRotation(-bias * 90);  
            }
            return;

        },

        linePixellength : function(from,to){ 
            return Math.sqrt(Math.abs(from.x- to.x) * Math.abs(from.x- to.x) + Math.abs(from.y- to.y) * Math.abs(from.y- to.y) );

        },
        pointToPoint : function(from,to ){
            return Math.abs(from.x- to.x) * Math.abs(from.x- to.x) + Math.abs(from.y- to.y) * Math.abs(from.y- to.y)

        },
        /**
         * �ƶ�����һ����
         * @param {Number} index ��ǰ�������.
         * @return �޷���ֵ.
         */
        _moveNext: function(index) {
            var me = this;
            if (index < this._path.length - 1) {
                me._move(me._path[index], me._path[index + 1], me._tween.linear);
            }
        },
        /**
         * ����С���Ϸ�infowindow�����ݣ�λ�õ�
         * @param {Point} pos ��γ�������.
         * @return �޷���ֵ.
         */
        _setInfoWin: function(pos) {
            //�����Ϸ�overlay��position
            var me = this;
            if(!me._overlay){
                return;
            }
            me._overlay.setPosition(pos, me._marker.getIcon().size);
            var index = me._troughPointIndex(pos);
            if (index != -1) {
                clearInterval(me._intervalFlag);
                me._overlay.setHtml(me._opts.landmarkPois[index].html);
                me._overlay.setPosition(pos, me._marker.getIcon().size);
                me._pauseForView(index);
            }else {
                me._overlay.setHtml(me._opts.defaultContent);
            }
        },
        /**
         * ��ĳ������ͣ��ʱ��
         * @param {Number} index �������.
         * @return �޷���ֵ.
         */
        _pauseForView: function(index) {
            var me = this;
            var t = setTimeout(function() {
                //������һ����
                me._moveNext(++me.i);
            },me._opts.landmarkPois[index].pauseTime * 1000);
            me._setTimeoutQuene.push(t);
        },
         //�����ͣ���ٿ�ʼ���е�timeout
        _clearTimeout: function() {
            for (var i in this._setTimeoutQuene) {
                clearTimeout(this._setTimeoutQuene[i]);
            }
            this._setTimeoutQuene.length = 0;
        },
         //����Ч��
        _tween: {
            //��ʼ���꣬Ŀ�����꣬��ǰ�Ĳ������ܵĲ���
            linear: function(initPos, targetPos, currentCount, count) {
                var b = initPos, c = targetPos - initPos, t = currentCount,
                d = count;
                return c * t / d + b;
            }
        },

        /**
         * �񾭹�ĳ�����index
         * @param {Point} markerPoi ��ǰС���������.
         * @return �޷���ֵ.
         */
        _troughPointIndex: function(markerPoi) {
            var t = this._opts.landmarkPois, distance;
            for (var i = 0, len = t.length; i < len; i++) {
                //landmarkPois�еĵ�û�г��ֹ��Ļ�
                if (!t[i].bShow) {
                    distance = this._map.getDistance(new BMap.Point(t[i].lng, t[i].lat), markerPoi);
                    //�������С��10�ף���Ϊ��ͬһ����
                    if (distance < 10) {
                        t[i].bShow = true;
                        return i;
                    }
                }
            }
           return -1;
        }
    });
    /**
     * �Զ����overlay����ʾ��С�����Ϸ�
     * @param {Point} Point Ҫ��λ�ĵ�.
     * @param {String} html overlay��Ҫ��ʾ�Ķ���.
     * @return �޷���ֵ.
     */
    function CustomOverlay(point,html) {
        this._point = point;
        this._html = html;
    }
    CustomOverlay.prototype = new BMap.Overlay();
    CustomOverlay.prototype.initialize = function(map) {
        var div = this._div = baidu.dom.create('div', {style: 'border:solid 1px #ccc;width:auto;min-width:50px;text-align:center;position:absolute;background:#fff;color:#000;font-size:12px;border-radius: 10px;padding:5px;white-space: nowrap;'});
        div.innerHTML = this._html;
        map.getPanes().floatPane.appendChild(div);
        this._map = map;
        return div;
    }
   CustomOverlay.prototype.draw = function() {
        this.setPosition(this.lushuMain._marker.getPosition(), this.lushuMain._marker.getIcon().size);
    }
    baidu.object.extend(CustomOverlay.prototype, {
        //����overlay��position
        setPosition: function(poi,markerSize) {
            // �˴���bug���޸�����л �綬(diligentcat@gmail.com) ��ϸ�Ĳ鿴������ָ��
            var px = this._map.pointToOverlayPixel(poi),
                styleW = baidu.dom.getStyle(this._div, 'width'),
                styleH = baidu.dom.getStyle(this._div, 'height');
                overlayW = parseInt(this._div.clientWidth || styleW, 10),
                overlayH = parseInt(this._div.clientHeight || styleH, 10);
            this._div.style.left = px.x - overlayW / 2 + 'px';
            this._div.style.bottom = -(px.y - markerSize.height) + 'px';
        },
        //����overlay������
        setHtml: function(html) {
            this._div.innerHTML = html;
        },
        //��customoverlay��ص�ʵ��������
        setRelatedClass: function(lushuMain) {
            this.lushuMain = lushuMain;
        }
    });
})();
