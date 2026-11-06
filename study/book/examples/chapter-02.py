"""خط لولهٔ کوچک با کتابخانهٔ استاندارد؛ دادهٔ مصنوعی، نه دادهٔ مسکن کتاب."""
from math import sqrt
from statistics import median, mean
import json


def fit(training_x, training_y):
    fill = median(x for x in training_x if x is not None)
    x = [fill if value is None else value for value in training_x]
    x_mean, y_mean = mean(x), mean(training_y)
    denominator = sum((v - x_mean) ** 2 for v in x)
    if denominator == 0:
        raise ValueError("A constant feature cannot identify this slope")
    weight = sum((v - x_mean) * (y - y_mean)
                 for v, y in zip(x, training_y)) / denominator
    return {"fill": fill, "weight": weight,
            "bias": y_mean - weight * x_mean, "baseline": y_mean}


def predict(model, values):
    return [model["weight"] * (model["fill"] if x is None else x)
            + model["bias"] for x in values]


def rmse(actual, predicted):
    return sqrt(mean((a - p) ** 2 for a, p in zip(actual, predicted)))


def run_demo():
    # آزمون را پیش از یادگیری میانه و وزن‌ها جدا کرده‌ایم.
    train_x, train_y = [1, 2, None, 4, 5], [5, 7, 9, 11, 13]
    test_x, test_y = [6, None], [15, 9]
    model = fit(train_x, train_y)
    result = predict(model, test_x)
    assert model["fill"] == 3
    assert result == [15, 9]
    return {"model": model, "predictions": result,
            "rmse": rmse(test_y, result),
            "baseline_rmse": rmse(test_y, [model["baseline"]] * 2)}


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
