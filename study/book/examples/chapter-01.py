"""نمونهٔ مستقل آموزشی: طبقه‌بندی با نزدیک‌ترین همسایه‌ها؛ دادهٔ ساختگی."""
from collections import Counter
from math import dist
import json


def predict(training, point, k=3):
    if not 1 <= k <= len(training):
        raise ValueError("k must be between one and the training size")
    nearest = sorted(training, key=lambda row: dist(row[0], point))[:k]
    counts = Counter(label for _, label in nearest)
    return min(counts, key=lambda label: (-counts[label], label))


def run_demo():
    training = [((1, 1), 0), ((2, 1), 0), ((1, 2), 0),
                ((7, 7), 1), ((8, 7), 1), ((7, 8), 1)]
    predictions = [predict(training, p) for p in [(2, 2), (7, 7)]]
    assert predictions == [0, 1]
    # این دو پرس‌وجو، ارزیابی مستقل روی دادهٔ واقعی نیستند.
    return {"predictions": predictions, "training_size": len(training)}


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
