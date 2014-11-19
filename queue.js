module.exports = (function () {

    var Queue = function (name, maxPoolSize) {
        var _this = this;

        name === undefined ? _this.name = "unnamed" : _this.name = name;

        _this.pendingPool = [];
        _this.activePool = [];
        _this.maxPoolSize = maxPoolSize || 20;

        // Listeners
        var onCompleteCallback = null;

        this.add = function (job) {
            _this.pendingPool.push(job);
        };

        this.start = function () {
            if (_this.pendingPool.length == 0) {
                dispatchOnComplete();
                return;
            }

            for (var i = 0; i < _this.pendingPool.length && _this.activePool.length < _this.maxPoolSize; i++) {
                var job = _this.pendingPool[i];

                if (!job || _this.activePool.indexOf(job) > -1)
                    continue;

                // console.log('i', i);

                // console.log('Active', _this.activePool.length);
                // console.log('Pending', _this.pendingPool.length);

                if (!job)
                    continue;

                job.setFinishedCallback(function () {
                    // console.log(this.args, job.args);
                    _this.removeJob(this);
                    _this.start();
                });

                job.run();

                setJobActive(job);
            }
        };

        var setJobActive = function (job) {
            _this.activePool.push(job);
        };

        this.removeJob = function (job) {
            var pendingPos = _this.pendingPool.indexOf(job);
            if (pendingPos > -1)
                _this.pendingPool.splice(pendingPos, 1);

            var activePos = _this.activePool.indexOf(job)
            if (activePos > -1)
                _this.activePool.splice(activePos, 1);
        };

        var dispatchOnComplete = function () {
            if (onCompleteCallback)
                onCompleteCallback();
        };

        this.setOnCompleteListener = function (fn) {
            onCompleteCallback = fn;
        };

    };

    var Job = function () {
        var _this = this;

        _this.callback = arguments[0];
        _this.args = Array.prototype.slice.call(arguments, 1);
        _this.finishedCallback = null;

        this.run = function () {
            _this.callback.apply(_this, _this.args);
        };

        this.finish = function () {
            if (_this.finishedCallback) {
                _this.finishedCallback();
            }
        };

        this.setFinishedCallback = function (callback) {
            _this.finishedCallback = callback;
        }

    };

    var obj2array = function (obj) {
        var out = [];
        for (var i in obj) {
            out.push(obj[i]);
        }
        return out;
    }

    return {
        queue: function (name) {
            return new Queue(name);
        },
        job: function () {
            var args = Array.prototype.slice.call(arguments);
            args.unshift(null);

            return new (Function.prototype.bind.apply(Job, args));
        },
        test: function () {
            return 'test';
        }
    }
})();