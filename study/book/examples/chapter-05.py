"""مدل خطی آموزشی با هزینهٔ hinge و گام تصادفی؛ جایگزین حل‌کنندهٔ SVC نیست."""
from math import exp
import json


def hinge(label, score):
    return max(0, 1 - label * score)


def train(rows, epochs=500, rate=0.01, c=3.0):
    weights, bias = [0.0, 0.0], 0.0
    for _ in range(epochs):
        for features, label in rows:
            score = sum(w * x for w, x in zip(weights, features)) + bias
            violated = label * score < 1
            weights = [w - rate * (w - (c * label * x if violated else 0))
                       for w, x in zip(weights, features)]
            if violated:
                bias += rate * c * label
    return weights, bias


def run_demo():
    rows = [((-2, -1), -1), ((-1, -2), -1), ((1, 2), 1), ((2, 1), 1)]
    weights, bias = train(rows)
    predictions = [1 if sum(w*x for w, x in zip(weights, f)) + bias >= 0 else -1
                   for f, _ in rows]
    assert predictions == [label for _, label in rows]
    assert hinge(1, 2) == 0
    assert exp(-2 * 1**2) < exp(-0.5 * 1**2)
    return {"weights": weights, "bias": bias, "training_predictions": predictions,
            "note": "Training predictions are not a generalization estimate"}


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
