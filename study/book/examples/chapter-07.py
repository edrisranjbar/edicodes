"""Bootstrap و OOB برای رأی‌گیری مدل‌های بسیار کوچک؛ نمونهٔ آموزشی مستقل."""
from collections import Counter
from random import Random
from statistics import mean
import json


def majority(values):
    counts = Counter(values)
    return min(counts, key=lambda label: (-counts[label], label))


def stump(rows):
    # مدل تک‌تقسیمی با آستانهٔ ثابت، برای روشن‌ماندن مفهوم bootstrap.
    left = [y for x, y in rows if x <= 5]
    right = [y for x, y in rows if x > 5]
    fallback = majority([y for _, y in rows])
    return (majority(left) if left else fallback,
            majority(right) if right else fallback)


def run_demo():
    rows = list(zip(range(1, 11), [0, 0, 0, 0, 1, 0, 1, 1, 1, 1]))
    rng, models = Random(42), []
    for _ in range(60):
        indices = [rng.randrange(len(rows)) for _ in rows]
        models.append((set(indices), stump([rows[i] for i in indices])))
    oob = []
    for index, (x, label) in enumerate(rows):
        votes = [prediction[0 if x <= 5 else 1]
                 for selected, prediction in models if index not in selected]
        if votes:
            oob.append(majority(votes) == label)
    probabilities = [0.49, 0.49, 0.99]
    hard = majority([int(p >= 0.5) for p in probabilities])
    soft = int(mean(probabilities) >= 0.5)
    assert hard == 0 and soft == 1
    assert len(oob) == len(rows)
    return {"hard_vote": hard, "soft_vote": soft, "mean_probability": mean(probabilities),
            "oob_covered_rows": len(oob), "oob_accuracy": mean(oob),
            "note": "OOB is not the independent final test"}


if __name__ == "__main__":
    print(json.dumps(run_demo(), ensure_ascii=False))
